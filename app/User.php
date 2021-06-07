<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Payroll\Models\Employee;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const PERMISSIONS = [
        'Create'    => 'user.create',
        'Read'      => 'user.read',
        'Update'    => 'user.update',
        'Delete'    => 'user.delete'
    ];

    protected $connection = 'mysql';

    protected $loginNames = ['email'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'organization_id', 'email', 'password', 'permissions', 'change_password', 'last_login',
        'is_master', 'activation_code', 'is_activated', 'database'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

//    public function findForPassport($username)
//    {
//        return $this->where('username', $username)->first();
//    }

    public static function register($credentials = array(), $permissions = array())
    {
        $credentials['password'] = bcrypt($credentials['password']);

        $credentials['permissions'] = json_encode($permissions);

        return self::create($credentials);
    }

    public function hasAccess($permission)
    {
        $userPermission = (array) json_decode($this->permissions);
        $userPermission = array_filter($userPermission, function ($perm) {
            return $perm;
        });

        $userPermission = array_keys($userPermission);

        return in_array($permission, $userPermission);
    }


    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function isArchived()
    {
        return $this->trashed();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public static function setOrganization()
    {
        \Session::put('ORG', \Auth::user()->organization);
    }

    public static function getOrganization()
    {
        return \Session::get('ORG');
    }
}
