<?php

namespace App\Http\Controllers;

use App\Mail\UserRegistered;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use Payroll\Models\Advance;
use Payroll\Models\Branches;
use Payroll\Models\Deduction;
use Payroll\Models\Department;
use Payroll\Models\Employee;
use Payroll\Models\EmployeeContract;
use Payroll\Models\Loans;
use Payroll\Models\Payroll;

class PagesController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $wages = EmployeeContract::current()
            ->select(DB::raw('distinct employee_id'))
            ->sum('current_basic_salary');

        $employees = Employee::count();
        $departments = Department::count();
        $branches = Branches::count();
        $unpaidLoans = Loans::where('balance', '>', 0)->count();

        list($basicPay, $minBasic, $maxBasic) = $this->getBasicPayDetails();
        list($loans, $minLoan, $maxLoan) = $this->getLoanDetails();
        list($advances, $minAdvance, $maxAdvance) = $this->getAdvanceDetails();

        return view('modules.dashboard')
            ->with('employees', $employees)
            ->with('departments', $departments)
            ->with('branches', $branches)
            ->with('basicPay', $basicPay)
            ->with('minBasic', $minBasic)
            ->with('maxBasic', $maxBasic)
            ->with('unpaidLoans', $unpaidLoans)
            ->with('loans', $loans)
            ->with('minLoan', $minLoan)
            ->with('maxLoan', $maxLoan)
            ->with('advances', $advances)
            ->with('minAdvance', $minAdvance)
            ->with('maxAdvance', $maxAdvance)
            ->with('wages', $wages);
    }

    /**
     * @return array
     */
    private function getBasicPayDetails()
    {
        $basicPay = Payroll::select(DB::raw('payroll_date, sum(basic_pay) as pay'))
            ->groupBy('payroll_date')
            ->where('payroll_date', '>=', Carbon::now()->startOfMonth()->subMonth(12)->startOfMonth())
            ->orderBy('payroll_date')
            ->get();

        $minBasic = 0;
        $maxBasic = 0;

        if (count($basicPay)) {
            $minBasic = $basicPay->min('pay');
            $maxBasic = $basicPay->max('pay');
        }

        $basicPay = $basicPay->toArray();

        if (count($basicPay) < 12) {
            $first = Carbon::now()->startOfMonth();
            if (isset($basicPay[0])) {
                $first = Carbon::parse($basicPay[0]['payroll_date'])->startOfMonth();
            }

            $difference = 12 - count($basicPay);

            for ($x = 1; $x <= $difference; $x++) {
                array_unshift($basicPay, [
                    'payroll_date' => $first->subMonth(1)->toDateTimeString(),
                    'pay'          => 0
                ]);
            }
        }

        $basicPay = array_map(function ($pay) {
            $pay['date'] = Carbon::parse($pay['payroll_date'])->format('M');
            unset($pay['payroll_date']);

            return $pay;
        }, $basicPay);

        return [$basicPay, $minBasic, $maxBasic];
    }

    private function getLoanDetails()
    {
        $loans = Loans::select(DB::raw('date_processed, sum(amount) as loan_amount'))
            ->groupBy('date_processed')
            ->where('date_processed', '>=', Carbon::now()->startOfMonth()->subMonth(12)->startOfMonth())
            ->orderBy('date_processed')
            ->get();

        $min = 0;
        $max = 0;

        if (count($loans)) {
            $min = $loans->min('loan_amount');
            $max = $loans->max('loan_amount');
        }

        $loans = $loans->toArray();

        if (count($loans) < 12) {
            $first = Carbon::now()->startOfMonth();
            if (isset($basicPay[0])) {
                $first = Carbon::parse($loans[0]['date_processed'])->startOfMonth();
            }
            $difference = 12 - count($loans);

            for ($x = 1; $x <= $difference; $x++) {
                array_unshift($loans, [
                    'date_processed' => $first->subMonth(1)->toDateTimeString(),
                    'loan_amount'          => 0
                ]);
            }
        }

        $loans = array_map(function ($pay) {
            $pay['date'] = Carbon::parse($pay['date_processed'])->format('M');
            unset($pay['date_processed']);

            return $pay;
        }, $loans);

        return [$loans, $min, $max];
    }

    private function getAdvanceDetails()
    {
        $advance = Advance::select(DB::raw('for_month, sum(amount) as amount'))
            ->groupBy('for_month')
            ->where('for_month', '>=', Carbon::now()->startOfMonth()->subMonth(12)->startOfMonth())
            ->orderBy('for_month')
            ->get();

        $min = 0;
        $max = 0;

        if (count($advance)) {
            $min = $advance->min('amount');
            $max = $advance->max('amount');
        }

        $advance = $advance->toArray();

        if (count($advance) < 12) {
            $first = Carbon::now()->startOfMonth();
            if (isset($basicPay[0])) {
                $first = Carbon::parse($advance[0]['for_month'])->startOfMonth();
            }
            $difference = 12 - count($advance);

            for ($x = 1; $x <= $difference; $x++) {
                array_unshift($advance, [
                    'for_month' => $first->subMonth(1)->toDateTimeString(),
                    'amount' => 0
                ]);
            }
        }

        $advance = array_map(function ($pay) {
            $pay['date'] = Carbon::parse($pay['for_month'])->format('M');
            unset($pay['for_month']);

            return $pay;
        }, $advance);

        return [$advance, $min, $max];
    }

    
}
