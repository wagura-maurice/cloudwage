@extends('layout')

@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>API Settings - <small> Set integrations with your payroll system.</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('users.index') }}">API Settings</a>
        </li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            <passport-clients></passport-clients>
            <passport-authorized-clients></passport-authorized-clients>
            <passport-personal-access-tokens></passport-personal-access-tokens>
        </div>
    </div>

@endsection

@section('footer')
    <script src="{{ mix('js/app.js') }}"></script>
@endsection