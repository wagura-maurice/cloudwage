<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Payroll\Models\Employee;
use Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pagination = request('paginate') ?: 30;
        $employees = Employee::when(request('departments'), function ($builder) {
            return $builder->join('assignments', 'assignments.employee_id', '=', 'employees.id')
                ->join('departments', 'assignments.department_id', '=', 'departments.id')
                ->whereIn('departments.id', explode(',', request('departments')))
                ->select([
                    'employees.id', 'departments.id as department_id', 'payroll_number', 'avatar',
                    'identification_number', 'identification_type', 'first_name', 'last_name', 'email', 'mobile_phone',
                    'has_custom_tax_rate', 'custom_tax_rate'
                ]);
        })
            ->paginate(intval($pagination));

        return Response::json($employees);
    }
}
