<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Payroll\Models\Department;
use Response;

class UserController extends Controller
{
    public function user(Request $request)
    {
        return $request->user();
    }
}
