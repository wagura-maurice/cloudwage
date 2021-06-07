<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'name', 'price', 'branches_cap', 'department_cap', 'employee_cap', 'payroll_cap', 'is_active',
    ];
}
