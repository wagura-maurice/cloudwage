@extends('layout')

@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Payment Structures - <small> Set up the payment structures that can be used within the organization</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('payment-structures.index') }}">Payment Structures</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">Current Payment Structures</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('payment-structures.create') }}" class="btn btn-transparent grey-salsa btn-circle btn-sm active"><i class="fa fa-plus"></i> Add Payment Structure</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-responsive dataTable">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Description
                                </th>
                                <th>
                                    Payment Per
                                </th>
                                <th>
                                    Edit
                                </th>
                                <th>
                                    Delete
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($structures as $structure)
                            <tr>
                                <td>
                                    <a href="{{ route('payment-structures.show', $structure->id) }}">{{ $structure->name }}</a>
                                </td>
                                <td>
                                    {{ $structure->description }}
                                </td>
                                <td>
                                    {{ $structure->unit }}
                                </td>
                                <td>
                                    <a class="btn btn-success btn-xs" href="{{ route('payment-structures.edit', $structure->id) }}">
                                        Edit </a>
                                </td>
                                <td>
                                    @if(! $structure->is_system)
                                        <a href="{{ route('payment-structures.destroy', $structure->id) }}" class="btn btn-danger btn-xs" data-method="delete" rel="nofollow" data-confirm="Are you sure you want to delete this?" data-token="{{ csrf_token() }}">Delete</a>
                                    @endif
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

