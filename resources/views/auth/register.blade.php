@extends('layouts.app')
@section('header')
    <style>body{background: #f8f8f8;}#cont{padding-top:70px;}nav ul a{color:#000;}input,label{color:#000 !important;}#login-logo{width: 250px;display: block;margin: 8px auto;}label:after{font-size:16px;}.input-field label{height:inherit;}</style>
@endsection
@section('content')
<div class="container" id="cont">
    <div class="row">
        <div class="col s6 offset-s3">
            <div class="card-panel">
                <img src="{{ asset('/images/logo.png') }}" alt="CloudWage" id="login-logo">
                <form class="form-horizontal" method="POST" action="{{ url('/register') }}">
                    {!! csrf_field() !!}

                    <div class="input-field">
                        <input value="{{ old('organization_name') }}" name="organization_name" id="organization_name" type="text" class="validate{{ $errors->has('organization_name') ? ' invalid' : '' }}" required>
                        <label for="organization_name" data-error="{{ $errors->first('organization_name') }}">Organization Name</label>
                    </div>

                    <div class="input-field">
                        <input value="{{ old('name') }}" name="name" id="name" type="text" class="validate{{ $errors->has('name') ? ' invalid' : '' }}" required>
                        <label for="name" data-error="{{ $errors->first('name') }}">Full Name</label>
                    </div>
                    {{--<div class="input-field">--}}
                        {{--<input value="{{ old('username') }}" name="username" id="username" type="text" class="validate{{ $errors->has('username') ? ' invalid' : '' }}" required>--}}
                        {{--<label for="username" data-error="{{ $errors->first('username') }}">Username</label>--}}
                    {{--</div>--}}
                    <div class="input-field">
                        <input value="{{ old('email') }}" name="email" id="email" type="email" class="validate{{ $errors->has('email') ? ' invalid' : '' }}" required>
                        <label for="email" data-error="{{ $errors->first('email') }}">Email</label>
                    </div>


                    <div class="input-field{{ $errors->has('password') ? ' invalid' : '' }}">
                        <input name="password" id="password" type="password" class="validate" required>
                        <label for="password" data-error="{{ $errors->first('password') }}">Password</label>
                    </div>

                    <div class="input-field{{ $errors->has('password_confirmation') ? ' invalid' : '' }}">
                        <input name="password_confirmation" id="password_confirmation" type="password" class="validate" required>
                        <label for="password_confirmation" data-error="{{ $errors->first('password_confirmation') }}">Confirm Password</label>
                    </div>

                    <button class="waves-effect waves-light btn">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
