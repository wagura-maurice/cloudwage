<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'plan_id', 'organization_id', 'start_date', 'end_date', 'amount_paid',
    ];
}
