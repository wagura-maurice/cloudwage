<?php

use App\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('organizations')->insertGetId([
            'name' => 'Wizag Core',
            'is_active' => true,
            'subscription_end' => null,
            'branches_cap' => null,
            'department_cap' => null,
            'employee_cap' => null,
            'payroll_cap' => null,
        ]);

        $permissions = [
            "superuser" => true,
            "user.read" => true,
            "user.create" => true,
            "user.update" => true,
            "user.delete" => true,
            "company.read" => true,
            "company.update" => true,
            "branch.read" => true,
            "branch.create" => true,
            "branch.update" => true,
            "branch.delete" => true,
            "department.read" => true,
            "department.create" => true,
            "department.update" => true,
            "department.delete" => true,
            "allowance.read" => true,
            "allowance.create" => true,
            "allowance.update" => true,
            "allowance.delete" => true,
            "deduction.read" => true,
            "deduction.create" => true,
            "deduction.update" => true,
            "deduction.delete" => true,
            "employee_type.read" => true,
            "employee_type.create" => true,
            "employee_type.update" => true,
            "employee_type.delete" => true,
            "policy.read" => true,
            "policy.update" => true,
            "shift.read" => true,
            "shift.create" => true,
            "shift.update" => true,
            "shift.delete" => true,
            "work_plan.read" => true,
            "work_plan.create" => true,
            "work_plan.update" => true,
            "work_plan.delete" => true,
            "holiday.read" => true,
            "holiday.create" => true,
            "holiday.update" => true,
            "holiday.delete" => true,
            "payment_method.read" => true,
            "payment_method.create" => true,
            "payment_method.update" => true,
            "payment_method.delete" => true,
            "payment_structure.read" => true,
            "payment_structure.create" => true,
            "payment_structure.update" => true,
            "payment_structure.delete" => true,
            "pay_grade.read" => true,
            "pay_grade.create" => true,
            "pay_grade.update" => true,
            "pay_grade.delete" => true,
            "employee.read" => true,
            "employee.create" => true,
            "employee.update" => true,
            "employee.delete" => true,
            "employee_contract.read" => true,
            "employee_contract.create" => true,
            "employee_contract.update" => true,
            "employee_contract.delete" => true,
            "assignment.read" => true,
            "assignment.create" => true,
            "assignment.update" => true,
            "assignment.delete" => true,
            "advance.read" => true,
            "advance.create" => true,
            "advance.update" => true,
            "advance.delete" => true,
            "loan.read" => true,
            "loan.create" => true,
            "loan.update" => true,
            "loan.delete" => true,
            "days_worked.read" => true,
            "days_worked.create" => true,
            "days_worked.update" => true,
            "days_worked.delete" => true,
            "hours_worked.read" => true,
            "hours_worked.create" => true,
            "hours_worked.update" => true,
            "hours_worked.delete" => true,
            "units_produced.read" => true,
            "units_produced.create" => true,
            "units_produced.update" => true,
            "units_produced.delete" => true,
            "payroll.read" => true,
            "payroll.create" => true,
            "payroll.update" => true,
            "payroll.delete" => true,
            "coinage.read" => true,
            "kra.read" => true,
            "krap10a.read" => true,
            "krap10b.read" => true
        ];

        $credentials = [
//            'username'    => 'smodav',
            'email'    => 'smodavprivate@gmail.com',
            'password' => '4dM1nPa$$',
            'change_password' => true,
            'organization_id' => $id,
            'is_master' => true,
        ];

        User::register($credentials, $permissions);

        $credentials = [
//            'username'    => 'admin',
            'email'    => 'development@wizag.biz',
            'password' => '4dM1nPa$$',
            'change_password' => true,
            'organization_id' => $id,
            'is_master' => true,
        ];

        user::register($credentials, $permissions);
    }
}
