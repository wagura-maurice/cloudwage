<?php

namespace Payroll\Models;

use Illuminate\Database\Eloquent\Model;
use Payroll\Repositories\PolicyRepository;

class Policy extends Model
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::updated(function () {
            PolicyRepository::reCache();
        });
    }

    const MODULE_ID = 30;
    const PAYROLL_PREFIX = 'PREFIX OF THE EMPLOYEE PAYROLL NUMBER';
    const ADVANCE_PER_JOB_GROUP = 'ADVANCE PER JOB GROUP';


    protected $fillable = ['module_id', 'policy', 'value', 'exceptions'];

    const PERMISSIONS = [
        'Create'    => 'policy.create',
        'Read'      => 'policy.read',
        'Update'    => 'policy.update',
        'Delete'    => 'policy.delete'
    ];

    public function scopeEnabled($query)
    {
        return $query->whereEnabled(true);
    }

    public function scopeDisabled($query)
    {
        return $query->whereEnabled(false);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

//    public function employeeContract()
//    {
//        return $this->hasMany(EmployeeContract::class);
//    }
    public static function getValue($name)
    {
        $policy = Policy::where('policy', $name)->first();

        return $policy ? $policy->value : null;
    }
}
