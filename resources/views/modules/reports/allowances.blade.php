@extends('layout')

@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Allowances - <small> Set up the allowances that can be given to employees in the organization</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('allowances.index') }}">Allowances</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">Current Allowances</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('allowances.create') }}" class="btn btn-transparent grey-salsa btn-circle btn-sm active"><i class="fa fa-plus"></i> Add Allowance</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-hover table-responsive dataTable" id="allowances_table">
                        <thead>
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                Type
                            </th>
                            <th>
                                Rate
                            </th>
                            <th>
                                Has Relief?
                            </th>
                            <th>
                                Taxable
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allowances as $allowance)
                            <tr>
                                <td>
                                    <a href="{{ route('allowances.generate', $allowance->id) }}">{{ $allowance->name }}</a>
                                </td>
                                <td class="text-right">
                                    {{ $allowance->type == 'rate' ? 'Fixed Rate' : 'Per Employee' }}
                                </td>
                                <td class="text-right">
                                    {{ $allowance->rate }}
                                </td>
                                <td class="text-center">
                                    {{ $allowance->has_relief == 1 ? 'Yes' : 'No' }}
                                </td>
                                <td class="text-center">
                                    {{ $allowance->taxable == 1 ? 'Yes' : 'No' }}
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

