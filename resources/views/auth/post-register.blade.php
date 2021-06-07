@extends('layouts.app')
@section('header')
    <style>body{background: #f8f8f8;}#cont{padding-top:70px;}nav ul a,p,h4{color:#000;}input,label{color:#000 !important;}#login-logo{width: 250px;display: block;margin: 8px auto;}label:after{font-size:16px;}</style>
@endsection
@section('content')
<div class="container" id="cont">
    <div class="row">
        <div class="col s8 offset-s2">
            <div class="card-panel">
                <img src="{{ asset('/images/logo.png') }}" alt="CloudWage" id="login-logo">
                <h4 class="center">{{ $title }}</h4>
                <p class="center">
                    {{ $message }}
                </p>
                @if(isset($button))
                    <a href="{{ $button['url'] }}" class="btn " style="display: block">{{ $button['name'] }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
