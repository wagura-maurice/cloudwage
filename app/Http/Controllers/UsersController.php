<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Policies\Policy;
use App\User;
use Auth;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Hash;
use Illuminate\Http\Request;

use App\Http\Requests;
use Payroll\Handlers\Authentication\Authenticator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Policy::canRead(new User());

        return view('modules.settings.users.index')
            ->with('users', User::where('organization_id', Auth::user()->organization_id)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Policy::canCreate(new User());

        return view('modules.settings.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        Policy::canCreate(new User());
        $permissions = [];

        if ($request->has('permissions')) {
            foreach ($request->get('permissions') as $permission) {
                $permissions[$permission] = true;
            }
        }

        $data = $request->only(['email', 'password']);
        $data['organization_id'] = Auth::user()->organization_id;
        $data['database'] = Auth::user()->database;
        $data['is_activated'] = true;

        User::register($data, $permissions);

        flash('Successfully added new user', 'success');

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $userId
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($userid)
    {
        Policy::canUpdate(new User());

        $user = User::where('organization_id', Auth::user()->organization_id)
            ->where('id', $userid)
            ->firstOrFail();

        $user->permissions = array_keys(get_object_vars(json_decode($user->permissions)));

        return view('modules.settings.users.edit')->withUser($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userid
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $userid)
    {
        Policy::canUpdate(new User());

        $user = User::findOrFail($userid);
        $permissions = [];
        if ($request->has('permissions')) {
            foreach ($request->get('permissions') as $permission) {
                $permissions[$permission] = true;
            }
        }

        $user->fill($request->all());
        $user->permissions = json_encode($permissions);
        $user->save();
        flash('Successfully edited user details', 'success');

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $userid
     * @return \Illuminate\Http\Response
     */
    public function destroy($userid)
    {
        Policy::canDelete(new User());

        $user = User::findOrFail($userid);
        $user->delete();
        flash('Successfully deleted user', 'success');

        return redirect()->route('users.index');
    }

    public function profile()
    {
        return view('modules.settings.users.profile')->withUser(Auth::user());
    }

    public function postProfile(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->get('old_password'), $user->getAuthPassword())) {
            flash('Sorry, old password is incorrect', 'error');

            return redirect()->back()->withInput();
        }

        $user->update([
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        flash('Successfully saved changes. Please sign in to continue.', 'success');

        Auth::logout();

        return redirect('/login')->withErrors(['message' => 'Successfully saved changes. Please sign in to continue.']);
    }
}
