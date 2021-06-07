<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Payroll\Models\Department;
use Response;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pagination = request('paginate') ?: 50;

        return Response::json(Department::select(['id', 'name'])->paginate(intval($pagination)));
    }
}
