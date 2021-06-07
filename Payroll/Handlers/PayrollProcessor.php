<?php

namespace Payroll\Handlers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Payroll\Models\Branches;
use Payroll\Models\CompanyProfile;
use Payroll\Models\Department;
use Payroll\Models\Employee;
use Payroll\Models\EmployeeLeave;
use Payroll\Models\EmployeeType;
use Payroll\Models\PayGrade;
use Payroll\Models\PaymentStructure;
use Payroll\Models\Payroll;
use Payroll\Models\Policy;
use Payroll\Parsers\ModelFilter;
use Payroll\Repositories\EmployeeRepository;
use Payroll\Repositories\OrganizationsRepository;

class PayrollProcessor
{
    /**
     * @var string
     */
    protected $db_connection;


    protected $modelFilter;

    /**
     * @var array
     */
    protected $employeeIds = [];

    protected $paymentStructure;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Collection
     */
    private $employees;

    /**
     * Create a new job instance.
     *
     * @param $request
     * @param $connection
     */
    public function __construct($request, $connection)
    {
        $this->request = $request;
        $this->db_connection = $connection;
        $this->modelFilter = new ModelFilter;
        $this->employees = collect();
        $this->employeeIds = [];
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        OrganizationsRepository::makeConfig($this->db_connection);
        \DB::setDefaultConnection($this->db_connection);

        $filters = $this->getFilters();
        $parent = json_decode($this->request['parent_filter']);
        $filtered = $filters->reject(function ($value) use ($parent) {
            return json_decode(json_encode($value)) != $parent;
        });

        $filter = $this->setUpEmployees($this->request, $filtered);

        $payrollDate = Carbon::parse('01-' . $this->request['payroll_date'])->endOfMonth()->setTime(0, 0);
        $company = CompanyProfile::first();
        $allowedLeaves = intval(Policy::where('policy', 'EXPECTED LEAVE DAYS')->first()->value);
        $policy = Policy::where('module_id', Payroll::MODULE_ID)->get();

        if ($this->paymentStructure) {
            $employeeQuery = EmployeeRepository::getRawBaseDetailsByPaymentStructure($this->paymentStructure);

            $employeeQuery->chunk(10, function ($employees) use ($payrollDate, $filter, $company, $allowedLeaves, $policy) {
                $this->processRoll($employees, $payrollDate, $filter, $company, $allowedLeaves, $policy);
            });

            return true;
        }

        $this->employeeIds = collect($this->employeeIds);

        foreach (collect($this->employeeIds)->chunk(10) as $ids) {
            $employees = EmployeeRepository::getRawBaseDetails($ids->toArray())->get();

            $this->processRoll($employees, $payrollDate, $filter, $company, $allowedLeaves, $policy);
        }

        return true;
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
     * @param $request
     * @param                $filtered
     *
     * @return mixed
     */
    private function setUpEmployees($request, $filtered)
    {
        $filter = 'Branches';
        $parentFil = $filtered->first()->first()->findOrFail($request['filter']);


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

        if ($filtered->first()->first() instanceof EmployeeType || $filtered->first()->first() instanceof PayGrade) {
            $parentFil = $filtered->first()->first()->findOrFail($request['filter']);

            $filter = 'Employee Type: ' . $parentFil->name;
            if ($filtered->first()->first() instanceof PayGrade) {
                $filter = 'Pay Grade: ' . $parentFil->name;
            }

            $results = $parentFil->contracts()->get(['employee_id'])->toArray();

            $this->employeeIds = array_merge($this->employeeIds, array_values(array_flatten($results)));
        }

        if ($filtered->first()->first() instanceof PaymentStructure) {
            $parentFil = $filtered->first()->first()
                ->findOrFail($request['filter']);
            $filter = 'Payment Structure: ' . $parentFil->name;

            $this->paymentStructure = $parentFil;
//            $this->employees = $parentFil->employees()
//                ->with([
//                    'contract', 'allowances', 'deductions', 'paymentStructure',
//                    'daysWorked', 'hoursWorked', 'unitsMade'
//                ])
//                ->get();
        }

        return $filter;
    }

    private function addToEmployees($employees)
    {
        $employees = $employees->map(function ($employee) {
            return $employee->id;
        })->toArray();

        $this->employeeIds = array_merge($this->employeeIds, $employees);

        return $this->employeeIds;
//        dd($employees);
//
//
//        $employees = EmployeeRepository::getBaseDetails($employees);
//
//        if ($this->employees->count() == 0) {
//            $this->employees = $employees;
//
//            return $this->employees;
//        }
//
//        $this->employees = $this->employees->merge($employees);
//
//        return $this->employees;
    }

    /**
     * @param $employees
     * @param $payrollDate
     * @param $filter
     */
    private function processRoll($employees, $payrollDate, $filter, $company, $allowedLeaves, $policy)
    {
        $employees = $this->modelFilter->filter($employees)
            ->usingKey('id')
            ->by(new Payroll)
            ->usingColumn('employee_id')
            ->wherePayrollDate($payrollDate->format('Y-m-d'))
            ->get();

        foreach ($employees as $employee) {
            if (!$result = PayrollCalculator::calculate($employee, $payrollDate, $filter)) {
                continue;
            }

            $payrollId = Payroll::insertGetId($result);

            $this->generatePDF($company, $payrollId, $allowedLeaves, $policy);
        }
    }

    private function generatePDF($company, $payrollId, $allowedLeaves, $policy)
    {
        $payroll = Payroll::find($payrollId);

        $payroll->employee = Employee::with([
            'leaves' => function ($builder) use ($payroll) {
                return $builder->where('status', EmployeeLeave::APPROVED)
                    ->where('start_date', '>=', Carbon::parse($payroll->payroll_date)->startOfYear())
                    ->where('end_date', '<=', Carbon::parse($payroll->payroll_date)->endOfYear());
            },
        ])->findOrFail($payroll->employee_id);

        $takenLeaves = $payroll->employee->leaves->reduce(function ($a, $b) {
            if (! $a) {
                return intval($b->days);
            }

            return intval($a->days) + intval($b->days);
        });

        $remainingLeaves = $allowedLeaves - $takenLeaves;

        $view = view('modules.payroll.payroll.payroll')
            ->with('company', $company)
            ->with('payroll', $payroll)
            ->with('leaves', $remainingLeaves)
            ->with('policies', $policy)
            ->render();

        $view = str_replace("\n", '', $view);

        $pdf = new DOMPDF();
        $pdf->setOptions(new Options(['enable_remote' => true]));
        $pdf->loadHtml($view);
        $pdf->setPaper('A4');
        $pdf->render();

        $ownDirectory = 'payrolls/' . $this->db_connection;

        if (! is_dir($ownDirectory)) {
            mkdir($ownDirectory, 0755, true);
        }

        $filename = $ownDirectory . '/' . Carbon::parse($payroll->payroll_date)->format('Ymd') .
            '-' . $payroll->employee_id . '.pdf';

        file_put_contents($filename, $pdf->output());
    }
}
