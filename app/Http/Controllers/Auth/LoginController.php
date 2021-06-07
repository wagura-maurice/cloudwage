<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Payroll\Handlers\Authentication\Authenticator;
use Payroll\Requests\LoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $org = $user->organization;

        if (Carbon::parse($org->subscription_end)->lt(Carbon::now())) {
            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'Your subscription has expired. Contact support for more information.'
            ]);
        }

        if (! $user->is_activated) {
            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'You have not activated your account.'
            ]);
        }

        $user->update([
            'last_login' => Carbon::now()
        ]);

        $user->setOrganization();

        return redirect()->intended('/');
    }
}
