<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvanceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Str;
use Payroll\Factories\HTMLElementsFactory;
use Payroll\Models\Advance;
use Payroll\Models\Employee;
use Payroll\Models\EmployeeContract;
use Payroll\Models\PayGrade;
use Payroll\Models\Policy;
use Payroll\Parsers\BulkAssigner;
use Payroll\Parsers\ModelFilter;

class AdvancesController extends Controller
{
    const FIELD_NAME = 'advance_amount';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \App\Policies\Policy::canRead(new Advance());

        return view('modules.payroll.advances.index')
            ->withAdvances(Advance::with('employee')->orderBy('for_month')->get()->unique('for_month'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \App\Policies\Policy::canCreate(new Advance());

        return view('modules.payroll.advances.create')
            ->withEmployees(Employee::all())
            ->withAdvance(new Advance);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdvanceRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AdvanceRequest $request)
    {
        \App\Policies\Policy::canCreate(new Advance());

        $employee = Employee::with([
            'contract' => function ($builder) {
                return $builder->orderBy('start_date', 'desc')->take(1);
            },
            'contract.payGrade'
        ])
            ->findOrFail($request->get('employee_id'));

        $contract = $employee->contract->first();

        if (intval($contract->payGrade->gross_percentage) > 0) {
            $percentage = $contract->payGrade->gross_percentage;
        } else {
            $percentage = Policy::where('module_id', Advance::MODULE_ID)
                ->where('policy', Advance::GROSS_PAY_PERCENTAGE)
                ->first()
                ->value;
        }

        $usableAmount = (floatval($contract->current_basic_salary) * floatval($percentage)) / 100;

        if ($request->get('amount') > $usableAmount) {
            flash(
                'Sorry, you can only process an advance of ' . $percentage . '% of basic salary: '
                . number_format($usableAmount, 2),
                'error'
            );

            return redirect()->back()->withInput($request->all());
        }

        $data = $request->all();

        if ($request->has('for_month')) {
            $data['for_month'] = Carbon::parse('01-' . $data['for_month'])->endOfMonth();
        } else {
            $data['for_month']  = Carbon::now()->endOfMonth();
        }

        $advance = new Advance($data);
        $advance->balance = $advance->amount;
        $advance->status = 'Unpaid';

        if (! $advance->checkPolicies()) {
            return redirect()->back()->withInput();
        }

        $advance->save();
        flash('Successfully processed advance', 'success');

        return redirect()->route('advances.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \App\Policies\Policy::canRead(new Advance());

        if (count(explode('-', $id)) > 1) {
            $advances = Advance::where('for_month', '=', Carbon::parse('1-' . $id)->endOfMonth()->format('Y-m-d'))
                ->get();

            return view('modules.payroll.advances.show')
                ->withTitle(Carbon::parse('30-' . $id)->format('F Y'))
                ->withAdvances($advances);
        }

        $advances = Advance::where('for_month', '>=', Carbon::parse('01-01-' . $id)->format('Y-m-d'))
            ->where('for_month', '<=', Carbon::parse('31-12-' . $id)->format('Y-m-d'))
            ->get();

        return view('modules.payroll.advances.show')
            ->withTitle(Carbon::parse('01-01-' . $id)->format('Y'))
            ->withAdvances($advances);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \App\Policies\Policy::canUpdate(new Advance());

        $advance = Advance::with(['employee'])->findOrFail($id);

        return view('modules.payroll.advances.edit')->withAdvance($advance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \App\Policies\Policy::canUpdate(new Advance());

        $advance = Advance::findOrFail($id);
        $employee = Employee::with([
            'contract' => function ($builder) {
                return $builder->orderBy('start_date', 'desc')->take(1);
            },
            'contract.payGrade'
        ])
            ->findOrFail($advance->employee_id);

        $contract = $employee->contract->first();

        if (intval($contract->payGrade->gross_percentage) > 0) {
            $percentage = $contract->payGrade->gross_percentage;
        } else {
            $percentage = Policy::where('module_id', Advance::MODULE_ID)
                ->where('policy', Advance::GROSS_PAY_PERCENTAGE)
                ->first()
                ->value;
        }

        $usableAmount = (floatval($contract->current_basic_salary) * floatval($percentage)) / 100;

        if ($request->get('amount') > $usableAmount) {
            flash(
                'Sorry, you can only process an advance of ' . $percentage . '% of basic salary: '
                . number_format($usableAmount, 2),
                'error'
            );

            return redirect()->back()->withInput($request->all());
        }

        $data = $request->all();

        if ($request->has('for_month')) {
            $data['for_month'] = Carbon::parse('01-' . $data['for_month'])->endOfMonth();
        }
        $balanceDifference = $data['amount'] - $advance->balance;
        $advance->fill($data);
        $advance->balance += $balanceDifference;
        $advance->status = 'Paid';
        if ($advance->balance > 0) {
            $advance->status = 'Unpaid';
        }
        $advance->save();
        flash('Successfully updated advance', 'success');

        return redirect()->route('advances.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Policies\Policy::canDelete(new Advance());

        $advance = Advance::findOrFail($id);
        $advance->delete();
        flash('Successfully deleted advance', 'success');

        return redirect()->route('advances.index');
    }

    public function bulkAssign(ModelFilter $filter, BulkAssigner $assigner)
    {
        \App\Policies\Policy::canCreate(new Advance());

        $todaysMonth = Carbon::now()->endOfMonth();
        $employees = $employees = $filter->filter(new Employee())
            ->usingKey('id')
            ->by(new Advance)
            ->usingColumn('employee_id')
            ->whereForMonth($todaysMonth->format('Y-m-d'))
            ->get();


        $requiredFields [] = [
            'name' => self::FIELD_NAME,
            'type' => HTMLElementsFactory::TEXT
        ];

        $employees->each(function ($item, $key) use ($employees) {
            $employees[$key] = collect($item)->only([
                'id', 'payroll_number', 'first_name', 'last_name', 'identification_number'
            ]);
        });

        return $assigner->withRows($employees)
            ->withRequiredFields($requiredFields)
            ->withAssignTo($todaysMonth)
            ->withFormAction(route('advances.process'))
            ->getForm();
    }

    public function bulkProcess(Request $request)
    {
        \App\Policies\Policy::canCreate(new Advance());

        $data = collect($request->all());
        $fields = $data->filter(function ($value, $key) {
            return Str::startsWith($key, self::FIELD_NAME) && $value != '';
        });

        $now = Carbon::now();
        $insert = array();

        foreach ($fields as $key => $value) {
            $insert [] = [
                'employee_id' => substr($key, strlen(self::FIELD_NAME)),
                'for_month' => $data['assignment_id'],
                'status' => 'Unpaid',
                'amount' => $value,
                'balance' => $value,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        Advance::insert($insert);

        flash('Successfully processed advances for employees.', 'success');

        return redirect()->route('advances.index');
    }
}
