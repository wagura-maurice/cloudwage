<?php
/**
 * @category PHP
 * @author   David Mjomba <smodavprivate@gmail.com>
 */

namespace App\Policies;

use App\User;
use Auth;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;

class Policy
{

    public static function canCreate(Model $model)
    {
        static::authorize($model::PERMISSIONS['Create']);
    }

    public static function canRead(Model $model)
    {
        static::authorize($model::PERMISSIONS['Read']);
    }

    public static function canUpdate(Model $model)
    {
        static::authorize($model::PERMISSIONS['Update']);
    }

    public static function canDelete(Model $model)
    {
        static::authorize($model::PERMISSIONS['Delete']);
    }

    private static function authorize($permission)
    {
        $userPermission = (array) json_decode(Auth::user()->permissions);
        $userPermission = array_filter($userPermission, function ($perm) {
            return $perm;
        });

        $userPermission = array_keys($userPermission);

        if (! in_array($permission, $userPermission) && ! in_array('superuser', $userPermission)) {
            abort(403, 'Sorry, you do not have permission.');
        }
    }
}
