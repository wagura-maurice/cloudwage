<?php

namespace App\Http\Controllers;

use App\Policies\Policy;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Payroll\Factories\HTMLElementsFactory;
use Payroll\Models\DaysWorked;
use Payroll\Models\Employee;
use Payroll\Models\PaymentStructure;
use Payroll\Parsers\BulkAssigner;
use Payroll\Parsers\ModelFilter;
use Payroll\Parsers\TimeAttendanceHandler;

class DaysWorkedController extends Controller
{
    const FIELD_NAME = 'number_of_days';

    /**
     * @var DaysWorked
     */
    private $daysWorked;

    /**
     * DaysWorkedController constructor.
     *
     * @param DaysWorked $daysWorked
     */
    public function __construct(DaysWorked $daysWorked)
    {
        $this->daysWorked = $daysWorked;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Policy::canRead(new DaysWorked());

        $unique = $this->daysWorked->orderBy('for_month', 'DESC')->groupBy('for_month')->get();

        return view('modules.attendance.index')
            ->withTitle('Days Worked')
            ->withRoute('worked')
            ->withDaysWorked($unique);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param TimeAttendanceHandler $attendanceHandler
     * @param BulkAssigner          $assigner
     *
     * @return \Illuminate\Http\Response
     */
    public function create(TimeAttendanceHandler $attendanceHandler, BulkAssigner $assigner)
    {
        Policy::canCreate(new DaysWorked());

        $todaysMonth = Carbon::now()->endOfMonth();
        $employees = $attendanceHandler->getEmployees($this->daysWorked, 'Month');
        if (!$employees) {
            return redirect()->back();
        }

        $requiredFields [] = [
            'name' => self::FIELD_NAME,
            'type' => HTMLElementsFactory::NUMERIC
        ];

        $employees->each(function ($item, $key) use ($employees) {
            $employees[$key] = collect($item)->only([
                'id', 'payroll_number', 'first_name', 'last_name', 'identification_number'
            ]);
        });

        if ($employees->count() == 0) {
            flash('You have already entered the days worked for the month.', 'error');

            return redirect()->back();
        }

        return $assigner->withRows($employees)
            ->withRequiredFields($requiredFields)
            ->withAssignTo($todaysMonth)
            ->withFormAction(route('worked.store'))
            ->getForm();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param TimeAttendanceHandler     $attendanceHandler
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TimeAttendanceHandler $attendanceHandler)
    {
        Policy::canCreate(new DaysWorked());

        $data = collect($request->all());
        $insert = $attendanceHandler->processBulk($data, static::FIELD_NAME, 'days_worked');
        $this->daysWorked->insert($insert);
        flash('Successfully processed the days worked for employees.', 'success');

        return redirect()->route('worked.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Policy::canRead(new DaysWorked());

        $daysWorked = $this->daysWorked->with(['employee']);
        if (count(explode('-', $id)) > 1) {
            $daysWorked = $daysWorked
                ->where('for_month', '>=', Carbon::parse('01-' . $id))
                ->where('for_month', '<=', Carbon::parse('01-' . $id)->endOfMonth())
                ->get();

            return view('modules.attendance.show')
                ->withRoute('worked')
                ->withField('days_worked')
                ->withTitle(Carbon::parse('01-' . $id)->format('F Y'))
                ->withAttendance($daysWorked);
        }

        $daysWorked = $daysWorked
            ->where('for_month', '>=', Carbon::parse('01-01-' . $id))
            ->where('for_month', '<=', Carbon::parse('31-12-' . $id))
            ->get();

        return view('modules.attendance.show')
            ->withTitle(Carbon::parse('01-01-' . $id)->format('Y'))
            ->withField('days_worked')
            ->withRoute('worked')
            ->withAttendance($daysWorked);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Policy::canUpdate(new DaysWorked());

        $daysWorked = $this->daysWorked->with(['employee'])->findOrFail($id);

        return view('modules.attendance.edit')
            ->withTitle(Carbon::parse('01-01-' . $id)->format('Y'))
            ->withField('days_worked')
            ->withRoute('worked')
            ->withAttendance($daysWorked);
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
        Policy::canUpdate(new DaysWorked());

        $attendance = $this->daysWorked->findOrFail($id);
        $attendance->update([
            'days_worked' => $request->get('days_worked')
        ]);
        flash('Successfully edited the days worked', 'success');

        return redirect()->route('worked.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Policy::canDelete(new DaysWorked());

        $this->daysWorked->findOrFail($id)->delete();
        flash('Successfully deleted the days worked', 'success');

        return redirect()->route('worked.index');
    }

//    public function importExcel(Request $request)
//    {
//        if ($request->hasFile('days_worked')) {
////            dd($request->all());
//            // if ($request->employee->extension() == 'xls' || $request->employee->extension() == 'xlsx') {
//            $excel = Excel::load(
//                $request->days_worked,
//                function ($reader) {
//                });
//
//            $data = $request->except('_token');
//
////            dd($request->all());
//
//            $excel = $excel->get()->toArray();
//            dd($excel);
//            $columns = [
//                'employee_id',
//                'for_month', 'days_worked'
//            ];
//            $invalid_columns = array_diff($columns, array_keys($excel[0]));
//            if (! count($invalid_columns)) {
//                $data[] = [];
//                foreach ($excel as $key => $days_worked) {
//                    foreach (DaysWorked::get(['employee_id']) as $value) {
////                        dd($value, $days_worked);
//                        if (strcmp((int) $days_worked['employee_id'], $value->employee_id)) {
//                            $data[$key]['employee_id'] = $days_worked['employee_id'];
//                            $data[$key]['for_month'] = $days_worked['for_month'];
//                            $data[$key]['days_worked'] = $days_worked['days_worked'];
//                        }
////                      else {
////                            flash('The format of your data is incorrect check and try again', 'error');
////                        }
//
//                    }
//                }
//            }
//        }
//        dd($data);
//        \DB::table('days_workeds')->insert($data);
//        // }
//    }
    public function downloadExcel()
    {
        $data = Employee::get(['first_name', 'last_name', 'payroll_number', 'identification_number'])->toArray();
        $employees[] = [];

        foreach ($data as $key => $employee) {
            $employees[$key]['First Name']= $employee['first_name'];
            $employees[$key]['Last Name']= $employee['last_name'];
            $employees[$key]['Payroll Number']= $employee['payroll_number'];
            $employees[$key]['Identification Number']= $employee['identification_number'];
            $employees[$key]['Number Of Days Worked']= $employee['days_worked'];
        }

        return Excel::create('employee_attendance', function ($excel) use ($employees) {
            $excel->sheet('mySheet', function ($sheet) use ($employees) {
                $sheet->fromArray($employees);
            });
        })->download('xls');
    }

}

