@extends('layout')

@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Employee Contracts - <small> The current contracts that the organization has.</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('contracts.index') }}">Employee Contracts</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">Current Employee Types</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('contracts.create') }}" class="btn btn-transparent grey-salsa btn-circle btn-sm active"><i class="fa fa-plus"></i> Add New Contract</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-responsive dataTable" id="allowances_table">
                        <thead>
                            <tr>
                                <th>
                                    Employee
                                </th>
                                <th>
                                    Employee Type
                                </th>
                                <th>
                                    Pay Grade
                                </th>
                                <th>
                                    Contract Start
                                </th>
                                <th>
                                    Contract End
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>
                                    <a href="{{ route('employees.show', $contract->employee->id) }}">{{ $contract->employee->first_name . ' ' . $contract->employee->last_name }}</a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('employee-types.show', $contract->employeeType->id) }}">{{ $contract->employeeType->name }}</a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('grades.show', $contract->payGrade->id) }}">{{ $contract->payGrade->name }}</a>
                                </td>
                                <td class="text-center">
                                    {{ $contract->start_date->toFormattedDateString() }}
                                </td>
                                <td class="text-center">
                                    {{ $contract->end_date->toFormattedDateString() }}
                                </td>
                                <td class="text-right">
                                    @if(\Carbon\Carbon::now() > ($contract->end_date))
                                        <a class="btn btn-warning btn-xs" href="{{ route('renew-contract', $contract->id) }}">Renew </a>
                                    @endif
                                    <a class="btn btn-primary btn-xs" href="{{ route('contracts.show', $contract->id) }}">View </a>
                                    <a class="btn btn-success btn-xs" href="{{ route('contracts.edit', $contract->id) }}">Edit </a>
                                    <a href="{{ route('contracts.destroy', $contract->id) }}" class="btn btn-danger btn-xs" data-method="delete" rel="nofollow" data-confirm="Are you sure you want to delete this?" data-token="{{ csrf_token() }}">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>

@endsection

