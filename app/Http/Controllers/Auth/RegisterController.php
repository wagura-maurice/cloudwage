<?php

namespace App\Http\Controllers\Auth;

use App\Mail\UserRegistered;
use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Mail;
use Payroll\Repositories\OrganizationsRepository;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
//            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:mysql.users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        Mail::to($user)->send(new UserRegistered($user));

        return redirect('/registered');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
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
        $data['activation_code'] = uniqid('', true) . str_random(32);
        $data['permissions'] = json_encode($permissions);
        $organization = [
            'name' => $data['organization_name'],
            'is_active' => true,
            'subscription_end' => Carbon::now()->endOfDay()->addMonth(),
            'branches_cap' => 2,
            'department_cap' => 2,
            'employee_cap' => 2,
            'payroll_cap' => null,
        ];

        $repo = OrganizationsRepository::register($organization, $data);

        return $repo->getUser();
    }

    public function postRegister()
    {
        return view('auth.post-register')
            ->with('title', 'Successfully registered!')
            ->with('message', 'We have sent you a confirmation email. Please click the link to activate your account.');
    }

    public function activate($token)
    {
        $token = decrypt($token);

        User::where('activation_code', $token)->update([
            'is_activated' => true
        ]);

        return view('auth.post-register')
            ->with('title', 'Successfully activated account!')
            ->with('message', 'You have successfully activated your account. Please log in to continue.')
            ->with('button', ['url' => url('/login'), 'name' => 'Login']);
    }
}
