<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'name', 'is_active', 'subscription_end', 'branches_cap', 'department_cap', 'employee_cap', 'payroll_cap',
        'database'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
