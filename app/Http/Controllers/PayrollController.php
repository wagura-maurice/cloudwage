<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\PayrollRequest;
use App\Http\Requests\ReportRequest;
use App\Jobs\ProcessPayroll;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Payroll\Handlers\PayrollCalculator;
use Payroll\Handlers\PayrollProcessor;
use Payroll\Models\Advance;
use Payroll\Models\AdvancePayments;
use Payroll\Models\Allowance;
use Payroll\Models\Branches;
use Payroll\Models\CompanyProfile;
use Payroll\Models\DaysWorked;
use Payroll\Models\Deduction;
use Payroll\Models\DeductionPayments;
use Payroll\Models\Department;
use Payroll\Models\Employee;
use Payroll\Models\EmployeeAllowance;
use Payroll\Models\EmployeeDeduction;
use Payroll\Models\EmployeeLeave;
use Payroll\Models\EmployeeType;
use Payroll\Models\KRAP9;
use Payroll\Models\Leave;
use Payroll\Models\LoanPayments;
use Payroll\Models\Loans;
use Payroll\Models\PayGrade;
use Payroll\Models\PaymentStructure;
use Payroll\Models\Payroll;
use Payroll\Models\Policy;
use Payroll\Parsers\DocumentGenerator;
use Payroll\Parsers\ModelFilter;
use App\Policies\Policy as UserPolicy;
use Payroll\Repositories\EmployeeRepository;
use Payroll\Models\Policy as PolicyModel;

class PayrollController extends Controller
{
    /**
     * @var Payroll
     */
    private $payroll;
    /**
     * @var Employee
     */
    private $employee;
    /**
     * @var EmployeeDeduction
     */
    private $employeeDeduction;
    /**
     * @var EmployeeAllowance
     */
    private $employeeAllowance;

    private $filters;
    private $employees;
    private $modelFilter;

    /**
     * PayrollController constructor.
     *
     * @param Payroll           $payroll
     * @param Employee          $employee
     * @param EmployeeDeduction $employeeDeduction
     * @param EmployeeAllowance $employeeAllowance
     * @param ModelFilter       $modelFilter
     */
    public function __construct(Payroll $payroll, Employee $employee,
        EmployeeDeduction $employeeDeduction, EmployeeAllowance $employeeAllowance,
        ModelFilter $modelFilter) {
        $this->payroll = $payroll;
        $this->employee = $employee;
        $this->employeeDeduction = $employeeDeduction;
        $this->employeeAllowance = $employeeAllowance;
        $this->employees = collect();
        $this->modelFilter = $modelFilter;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        UserPolicy::canRead(new Payroll());

        return view('modules.payroll.payroll.index')
            ->withFilters($this->payroll->groupBy('filter')->get())
            ->withSubFilters($this->payroll->orderBy('payroll_date', 'DESC')->groupBy('payroll_date')->get())
            ->withAllurl('?all=true')
            ->withPayrolls($this->payroll->all())
            ->withCurrency(CompanyProfile::first()->currency);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $payrollDate = Carbon::now();
//        dd(PayrollCalculator::getLeaveDays($payrollDate));
        UserPolicy::canCreate(new Payroll());

        $this->filters = collect([
            'Branch' => Branches::get(['id', 'branch_name']),
            'Department' => Department::all(['id', 'name']),
            'Employee Type' => EmployeeType::all(['id', 'name']),
            'Pay Grade' => PayGrade::all(['id', 'name']),
            'Payment Structure' => PaymentStructure::all(['id', 'name'])
        ]);

        return view('modules.payroll.payroll.create')
            ->withEmployees($this->employee->all())
            ->withFilters($this->filters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PayrollRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayrollRequest $request)
    {
        UserPolicy::canCreate(new Payroll());

        $connection = getConnection();

        dispatch(new ProcessPayroll($request->all(), $connection));

        flash('Please wait as we generate the payroll for you. You can leave the page and come back later.', 'info');

        return redirect()->route('payroll.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int    $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        UserPolicy::canRead(new Payroll());

        $currency = CompanyProfile::first()->currency;
        $payrolls = $this->payroll;
        $subFil = $this->payroll;
        $allURL = '?all=true';
        $printUrl = '';

        if ($request->has('sub')) {
            $payrolls = $payrolls
                ->where('payroll_date', Carbon::parse('01-' . $request->get('sub'))->endOfMonth()->toDateString());
            $allURL .= '&sub=' . $request->get('sub');
        }

        if ($request->has('main')) {
            $payrolls = $payrolls->where('filter', $request->get('main'));
            $subFil = $subFil->where('filter', $request->get('main'));
            $allURL .= '&main=' . $request->get('main');
            $printUrl .= '&main=' . $request->get('main');
        }

        $payrolls = $payrolls->orderBy('payroll_date')->get();
        $subFil = $subFil->orderBy('payroll_date')->groupBy('payroll_date')->get();
        $finalized = $payrolls->where('finalized', 0)->count() > 0 ? false : true;

        if ($request->has('all')) {
            $view = view('modules.payroll.payroll.showPayrolls');
            if (! $finalized) {
                $view->withPayDate($payrolls->first()->payroll_date);
            }
            return $view->withAllurl($allURL . $printUrl)
                ->withPayrolls($payrolls);
        }

        if ($request->has('main') || $request->has('sub')) {
            $view = view('modules.payroll.payroll.index');
            if (! $finalized && $request->has('sub')) {
                $view->withPayDate($payrolls->first()->payroll_date);
            }

            return $view->withFilters($this->payroll->groupBy('filter')->get())
                ->withSubFilters($subFil)
                ->withAllurl($allURL)
                ->withPayrolls($payrolls)
                ->withCurrency($currency);
        }

        $payroll = $this->payroll->with('employee')->findOrFail($id);
        $enabled = Policy::whereModuleId(Payroll::MODULE_ID)
            ->wherePolicy(Payroll::ENABLE_DAYS_ATTENDANCE)
            ->first()->value;

        return view('modules.payroll.payroll.show')
            ->withEnabledDays($enabled)
            ->withPayroll($payroll)
            ->withCurrency($currency);
    }

    public function getPDF($id)
    {
        UserPolicy::canRead(new Payroll());

        $payroll = $this->payroll->findOrFail($id);

        $ownDirectory = 'payrolls/' . getConnection();

        $filename = $ownDirectory . '/' . Carbon::parse($payroll->payroll_date)->format('Ymd') .
            '-' . $payroll->employee_id . '.pdf';

        if (is_file($filename)) {
            header('Content-Type: application/pdf');

            return \response()->file($filename);
        }

        $company = CompanyProfile::first();

        $payroll->employee = Employee::with([
            'leaves' => function ($builder) use ($payroll) {
                return $builder->where('status', EmployeeLeave::APPROVED)
                    ->where('start_date', '>=', Carbon::parse($payroll->payroll_date)->startOfYear())
                    ->where('end_date', '<=', Carbon::parse($payroll->payroll_date)->endOfYear());
            },
        ])->findOrFail($payroll->employee_id);

        $allowedLeaves = intval(PolicyModel::where('policy', 'EXPECTED LEAVE DAYS')->first()->value);// TODO: use policy
        $takenLeaves = $payroll->employee->leaves->reduce(function ($a, $b) {
            if (! $a) {
                return intval($b->days);
            }

            return intval($a->days) + intval($b->days);
        });

        $remainingLeaves = $allowedLeaves - $takenLeaves;
        $policy = PolicyModel::where('module_id', Payroll::MODULE_ID)->get();

        $view = view('modules.payroll.payroll.payroll')
            ->withCompany($company)
            ->withPayroll($payroll)
            ->withLeaves($remainingLeaves)
            ->withPolicies($policy)
            ->render();

        $view = str_replace("\n", '', $view);

        $pdf = new DOMPDF();
        $pdf->setOptions(new Options(['enable_remote' => true]));
        $pdf->loadHtml($view);
        $pdf->setPaper('A4');
        $pdf->render();

        header('Content-Type: application/pdf');
        return $pdf->stream('payroll.pdf', ['Attachment' => 0]);
    }

    public function getAllPDFs(Request $request)
    {
        ini_set('max_execution_time', 300);
        UserPolicy::canRead(new Payroll());
        $payrolls = $this->getAllPayrolls($request);
//        $company = CompanyProfile::first();
//        $policy = PolicyModel::where('module_id', Payroll::MODULE_ID)->get();

//        $view = view('modules.payroll.payroll.allPayrolls')
//            ->with('company', $company)
//            ->withPolicies($policy)
//            ->with('payrolls', $payrolls)
//            ->render();
//
//        return $view;

//        $pdf = new DOMPDF();
//        $pdf->setOptions(new Options(['enable_remote' => true]));
//        $pdf->loadHtml($view);
//        $pdf->setPaper('A4');
//        $pdf->render();
//
//        header('Content-Type: application/pdf');
//
//        return $pdf->stream('payroll.pdf', ['Attachment' => 0]);
//
//        $ownDirectory = 'payrolls/' . getConnection();

        $ownDirectory = 'payrolls/' . getConnection();

        if (! is_dir($ownDirectory)) {
            mkdir($ownDirectory, 0755, true);
        }

        $filename = $ownDirectory . '/' . Carbon::parse($payrolls->payroll_date)->format('Ymd') . '.pdf';

        $files = File::glob($ownDirectory . '/' . Carbon::parse($payrolls->payroll_date)->format('Ymd') . '*.pdf');
        $merger = new Merger();
        $merger->addIterator($files);
        file_put_contents($filename, $merger->merge());

        return response()->file(public_path($filename));
    }

    public function viewAll($month)
    {
        UserPolicy::canRead(new Payroll());

        if (! preg_match('/-/', $month)) {
            return redirect()->route('payroll.index');
        }

        $month = Carbon::parse('01-'.$month)->endOfMonth()->toDateString();
        $payrolls = $this->payroll->with('employee')->wherePayrollDate($month)->get();

        return view('modules.payroll.payroll.showPayrolls')
            ->withPayrolls($payrolls);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        UserPolicy::canDelete(new Payroll());
        $payroll = Payroll::findOrFail($id);

        if ($payroll->finalized == 1) {
            flash('Sorry, the generated payroll has been finalized.', 'error');

            return redirect()->back();
        }

        $payroll->delete();
        flash('Successfully deleted payroll.', 'success');

        return redirect()->back();
    }

    public function report()
    {
        UserPolicy::canRead(new Payroll());

        $months = $this->payroll->all()->unique('payroll_date')->sort();
        $months = $months->each(function ($item, $key) use ($months) {
            $months[$key] = [
                'id' => $item->payroll_date,
                'value' => Carbon::parse($item->payroll_date)->format('d F Y')
            ];
        });

        return view('modules.payroll.payroll.report')
            ->withMonths($months);
    }

    public function generate(Request $request, DocumentGenerator $generator)
    {
        UserPolicy::canRead(new Payroll());

        return $generator->withModuleId(Payroll::MODULE_ID)
            ->setColumns($this->getColumns())
            ->withFormAction(route('payroll.document'))
            ->withItemId($request->get('payroll_date'))
            ->view();
    }

    private function getColumns()
    {
        $columns = collect();
        foreach (Schema::getColumnListing((new Payroll())->getTable()) as $column) {
            if ($column == 'created_at' ||
                $column == 'updated_at' ||
                $column == 'deleted_at' ||
                $column == 'currency_id' ||
                $column == 'updated_at' ||
                $column == 'deleted_at' ||
                $column == 'id' ||
                $column == 'employee_id' ||
                $column == 'currency_id' ||
                $column == 'allowance_id' ||
                $column == 'kra' ||
                $column == 'filter' ||
                $column == 'avatar' ||
                $column == 'finalized' ||
                $column == 'payment_structure_id' ||
                $column == 'for_rate'
            ) {
                continue;
            }

            $columns->push($column);
        }

        foreach (Schema::getColumnListing((new Employee())->getTable()) as $column) {
            if ($column == 'created_at' ||
                $column == 'updated_at' ||
                $column == 'deleted_at' ||
                $column == 'id' ||
                $column == 'employee_id' ||
                $column == 'currency_id' ||
                $column == 'allowance_id'
            ) {
                continue;
            }

            $columns->push($column);
        }

        return $columns->sort();
    }

    public function getDocument(ReportRequest $request, DocumentGenerator $generator)
    {
        UserPolicy::canRead(new Payroll());

        $request_details = $request->all();
        $report_fields = $request_details['report_fields'];

        $payrolls = $this->payroll
            ->with('employee')
            ->wherePayrollDate(Carbon::parse($request->get('item_id'))->format('Y-m-d'))
            ->get();

        $sumPay = new Payroll();
        $sumPay->id = null;

        foreach (Allowance::all() as $all) {
            $item = snake_case(str_replace('/', '_', $all->name));
            $sumPay->$item = 0;
        }

        foreach (Deduction::all() as $ded) {
            $item = snake_case(str_replace('/', '_', $ded->name));
            $sumPay->$item = 0;
        }
        $sumPay->allowances = 0;
        $sumPay->deductions = 0;
        $sumPay->advances = 0;
        $sumPay->loans = 0;

        $payrolls = $payrolls->each(function ($item) use ($sumPay) {
            $allowances = json_decode($item->allowances);
            $deductions = json_decode($item->deductions);
            $advances = json_decode($item->advances);
            $loans = json_decode($item->loans);
            $item->allowances = 0;
            $item->deductions = 0;
            $item->advances = 0;
            $item->loans = 0;

            foreach ($allowances as $allowance) {
                $item->allowances += $allowance->tax_amount;
                $sumPay->allowances += $allowance->tax_amount;
                $allowanceType = snake_case(str_replace('/', '_', $allowance->name));
                $item->$allowanceType = $allowance->tax_amount;
                $sumPay->$allowanceType += $allowance->tax_amount;
            }

            foreach ($deductions as $deduction) {
                $deductionType = snake_case(str_replace('/', '_', $deduction->name));
                if (is_object($deduction->amount)) {
                    $item->deductions += $deduction->amount->amount - $deduction->amount->relief->amount;
                    $sumPay->deductions += $deduction->amount->amount - $deduction->amount->relief->amount;
                    $item->$deductionType = $deduction->amount->amount - $deduction->amount->relief->amount;
                    $sumPay->$deductionType += $deduction->amount->amount - $deduction->amount->relief->amount;
                    continue;
                }
                $sumPay->deductions += $deduction->amount;
                $item->deductions += $deduction->amount;
                $sumPay->$deductionType += $deduction->amount;
                $item->$deductionType = $deduction->amount;
            }

            foreach ($advances as $advance) {
                $sumPay->advances += $advance->amount;
                $item->advances += $advance->amount;
            }

            foreach ($loans as $loan) {
                $sumPay->loans += $loan->amount;
                $item->loans += $loan->amount;
            }

            $sumPay->basic_pay += $item->basic_pay;
            $item->gross_pay = $item->basic_pay + $item->allowances;
            $sumPay->gross_pay += $item->basic_pay + $item->allowances;
            $item->net_pay = $item->gross_pay - ($item->deductions + $item->loans + $item->advances);
            $sumPay->net_pay += $item->gross_pay - ($item->deductions + $item->loans + $item->advances);
            $employee = collect($item->employee->getAttributes());
            $employee->each(function ($value, $key) use ($item, $sumPay) {
                $sumPay->$key = null;
                $item->$key = $value;
            });
        });



        $payrolls->push(new Payroll());
        $payrolls->push(new Payroll());
        $payrolls->push($sumPay);


        $order = $request->get('order');
        $orderArray = collect(explode(',', $order));
        $newOrder = collect();
        $orderArray->each(function ($value) use ($orderArray, $newOrder) {
            if ($value == 'allowances') {
                foreach (Allowance::all() as $all) {
                    $newOrder->push($all->name);
                }
            }

            if ($value == 'deductions') {
                foreach (Deduction::all() as $ded) {
                    $newOrder->push($ded->name);
                }
            }
            $newOrder->push($value);
        });
        $newOrder->push('gross_pay');
        $newOrder->push('net_pay');

        $request->request->remove('order');
        $request->request->add(['order' => implode(',', $newOrder->toArray())]);
        $generator->orientation = 'landscape';
        $generator = $generator->prepare($request);
        $document = $generator->withRows($payrolls)
            ->render();

        return $document;
    }

    public function deletePayrolls(Request $request)
    {
        UserPolicy::canDelete(new Payroll());

        $date = $request->get('date');
        $payrolls = Payroll::wherePayrollDate($date)->whereFinalized(0)->get();

        if (! $payrolls) {
            flash('Sorry, the generated payrolls has been finalized.', 'error');

            return redirect()->back();
        }

        $payroll = $payrolls->first();

        if ($payroll->finalized == 1) {
            flash('Sorry, the generated payroll has been finalized.', 'error');

            return redirect()->back();
        }

        Payroll::wherePayrollDate($date)->whereFinalized(0)->delete();

        flash('Successfully deleted payrolls.', 'success');

        return redirect('/payroll');
    }

    public function finalize(Request $request)
    {
        UserPolicy::canUpdate(new Payroll());

        if (! $request->has('date')) {
            abort(404);
        }
        $date = $request->get('date');
        $deductions = Deduction::all();
        $payroll = Payroll::wherePayrollDate($date)->whereFinalized(0)->get();
        $employees = Employee::with(['loans', 'deductions', 'advances'])->get()->keyBy('id');
        $payroll = $payroll->map(function ($value) {
            $value->deductions = json_decode($value->deductions);
            $value->loans = json_decode($value->loans);
            $value->advances = json_decode($value->advances);
            $value->kra = json_decode($value->kra);
            return $value;
        });

        // wrap in transaction
        DB::transaction(function () use ($payroll, $employees, $deductions, $date) {
            $this->finalizeAdvances($payroll, $employees);
            $this->finalizeLoans($payroll, $employees);
            $this->finalizeDeductions($payroll, $employees, $deductions);
            $this->finalizeKRA($payroll);
            Payroll::wherePayrollDate($date)->update(['finalized' => 1]);
        });

        flash('Successfully finalized payroll.', 'success');

        return redirect()->route('payroll.index');
    }

    private function finalizeDeductions($payroll, $employees, $deductions)
    {
        $today = Carbon::now();
        $inserts = collect();
        $payroll->map(function ($payroll) use ($employees, $deductions, $today, $inserts) {
            $employee = $employees->get($payroll->employee_id);
            return collect($payroll->deductions)
                ->map(function ($payrollDeductions) use ($payroll, $employee, $deductions, $today, $inserts) {
                    $deduction = $deductions->where('name', $payrollDeductions->name)->first();
                    $empDeduction = $employee->deductions->where('deduction_id', $deduction->id)->first();
                    $amount = is_object($payrollDeductions->amount) ?
                        ($payrollDeductions->amount->amount - $payrollDeductions->amount->relief->amount) :
                        $payrollDeductions->amount;

                    $insert = [
                        'deduction_id' => $deduction->id,
                        'employee_id' => $employee->id,
                        'deduction_number' => $empDeduction->deduction_number,
                        'amount' => $amount,
                        'for_month' => $payroll->payroll_date,
                        'created_at' => $today,
                        'updated_at' => $today
                    ];
                    $inserts->push($insert);

                    return $insert;
                });

        });

        DeductionPayments::insert($inserts->toArray());
    }

    private function finalizeKRA($payroll)
    {
        $payroll->map(function ($payroll) {
            KRAP9::create((array) $payroll->kra);
        });
    }

    private function finalizeAdvances($payroll, $employees)
    {
        $today = Carbon::now();
        $inserts = collect();
        $payroll->map(function ($payroll) use ($employees, $today, $inserts) {
            $employee = $employees->get($payroll->employee_id);
            return collect($payroll->advances)
                ->map(function ($payrollAdvances) use ($payroll, $employee, $today, $inserts) {
                    $advanceMonth = Carbon::parse('01-' . substr($payrollAdvances->name, 10))
                        ->endOfMonth()
                        ->setTime(0, 0, 0);

                    return $employee->advances
                        ->where('for_month', $advanceMonth)
                        ->where('status', Advance::STATUS_UNPAID)
                        ->map(function ($advance) use ($today, $inserts, $payrollAdvances) {
                            $insert = [
                                'advance_id' => $advance->id,
                                'employee_id' => $advance->employee_id,
                                'amount' => $payrollAdvances->amount,
                                'comment' => 'Advance - ' . $advance->for_month->format('F-Y'),
                                'created_at' => $today,
                                'updated_at' =>$today
                            ];
                            $advance->balance -= $payrollAdvances->amount;
                            $update = [];
                            $update['balance'] = $advance->balance;
                            if ($advance->balance < 1) {
                                $update['status'] = Advance::STATUS_PAID;
                            }
                            Advance::whereId($advance->id)->update($update);
                            $inserts->push($insert);

                            return $insert;
                        });
                });
        });
        AdvancePayments::insert($inserts->toArray());
    }

    private function finalizeLoans($payroll, $employees)
    {
        $today = Carbon::now();
        $inserts = collect();
        $payroll->map(function ($payroll) use ($employees, $today, $inserts) {
            $employee = $employees->get($payroll->employee_id);

            return collect($payroll->loans)
                ->map(function ($payrollLoans) use ($payroll, $employee, $today, $inserts) {
                    $loanMonth = Carbon::parse(substr(explode(':', $payrollLoans->name)[2], 1));

                    return $employee->loans
                        ->where('date_processed', $loanMonth)
                        ->reject(function ($loan) {
                            return $loan->balance < 1;
                        })
                        ->map(function ($loan) use ($today, $inserts, $payrollLoans) {

                            $insert = [
                                'loan_id' => $loan->id,
                                'amount' => $payrollLoans->amount,
                                'comment' => 'Loan: ' . number_format($loan->amount, 2) . ' borrowed: ' . $loan->date_processed->format('d-F-Y'),
                                'created_at' => $today,
                                'updated_at' =>$today
                            ];

                            $loan->balance -= $payrollLoans->amount;
                            $loan->payment_months_made += 1;

                            Loans::whereId($loan->id)->update([
                                'balance' => $loan->balance,
                                'payment_months_made' => $loan->payment_months_made
                            ]);
                            $inserts->push($insert);

                            return $insert;
                        });
                });
        });

        LoanPayments::insert($inserts->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Payroll
     */
    private function getAllPayrolls(Request $request)
    {
        $payrolls = $this->payroll->with('employee');
        if ($request->has('sub')) {
            $payrolls = $payrolls->where('payroll_date', Carbon::parse('01-' . $request->get('sub'))
                        ->endOfMonth()
                        ->toDateString());
        }

        if ($request->has('main')) {
            $payrolls = $payrolls->where('filter', $request->get('main'));
        }

        return $payrolls->orderBy('payroll_date')->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getFilters()
    {
        $filters = collect([
            'Branch'            => Branches::get(['id', 'branch_name']),
            'Department'        => Department::all(['id', 'name']),
            'Employee Type'     => EmployeeType::all(['id', 'name']),
            'Pay Grade'         => PayGrade::all(['id', 'name']),
            'Payment Structure' => PaymentStructure::all(['id', 'name'])
        ]);

        return $filters;
    }

    /**
     * @param PayrollRequest $request
     * @param                $filtered
     *
     * @return mixed
     */
    private function setUpEmployees(PayrollRequest $request, $filtered)
    {
        $filter = 'Branches';
        $parentFil = $filtered->first()->first()->findOrFail($request->get('filter'));

        if ($filtered->first()->first() instanceof Branches) {
            $filter = 'Branch: ' . $parentFil->branch_name;
            $results = $parentFil->departments()->with(['employees' => function ($query) {
                $query->select('employees.id');
            }])->get();
            $results->each(function ($item) {
                $this->addToEmployees($item->employees);
            });
        }

        if ($filtered->first()->first() instanceof Department) {
            $filter = 'Department: ' . $parentFil->name;
            $this->addToEmployees($parentFil->employees()->get(['employees.id']));
        }

        if ($filtered->first()->first() instanceof EmployeeType ||
            $filtered->first()->first() instanceof PayGrade
        ) {
            $parentFil = $filtered->first()->first()
                ->findOrFail($request->get('filter'));
            $results = $parentFil
                ->contracts()
                ->get();

            $filter = 'Employee Type: ' . $parentFil->name;
            if ($filtered->first()->first() instanceof PayGrade) {
                $filter = 'Pay Grade: ' . $parentFil->name;
            }

            foreach ($results as $result) {
                $currentEmp = $result->employee()
                    ->with(
                        ['contract', 'allowances', 'deductions', 'paymentStructure', 'daysWorked', 'hoursWorked', 'unitsMade'])
                    ->get();
                if ($this->employees->count() == 0) {
                    $this->employees = $currentEmp;
                } else {
                    $this->employees = $this->employees->merge($currentEmp);
                }
            }
        }

        if ($filtered->first()->first() instanceof PaymentStructure) {
            $parentFil = $filtered->first()->first()
                ->findOrFail($request->get('filter'));
            $filter = 'Payment Structure: ' . $parentFil->name;
            $this->employees = $parentFil->employees()
                ->with([
                    'contract', 'allowances', 'deductions', 'paymentStructure',
                    'daysWorked', 'hoursWorked', 'unitsMade'
                ])
                ->get();
        }

        return $filter;
    }

    private function addToEmployees($employees)
    {
        $employees = $employees->map(function ($employee) {
            return $employee->id;
        })->toArray();

        $employees = EmployeeRepository::getBaseDetails($employees);

        if ($this->employees->count() == 0) {
            $this->employees = $employees;

            return $this->employees;
        }
        $this->employees = $this->employees->merge($employees);

        return $this->employees;
    }
}
