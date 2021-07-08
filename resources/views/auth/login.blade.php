<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>CloudWage | Login</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/pages/css/login3.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/select2/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/components-rounded.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/layout4/css/layout.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/layout4/css/themes/default.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/layout4/css/custom.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<div class="errorDiv">
    @if(count($errors))
        <div class="alert alert-warning">
            @foreach($errors->all() as $error)
                {!! $error !!}
            @endforeach
        </div>
    @endif
</div>

<div class="page-content" style="background: url('/images/login_bg.jpg') no-repeat;background-size: cover;">
    <div class="form-holder">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="{{ ucwords(config('app.name')) }}">
        </div>
        <!-- BEGIN LOGIN FORM -->
        <form class="form-horizontal logindetail" role="form" method="POST" action="{{ url('/login') }}">
            {!! csrf_field() !!}
            <div class="alert alert-danger display-hide">
                <button class="close" data-close="alert"></button>
                <span>Enter your username and password. </span>
            </div>
            {{--<div class="form-group">--}}
                {{--<label class="control-label visible-ie8 visible-ie9">Username</label>--}}
                {{--<div class="input-icon">--}}
                    {{--<i class="fa fa-user"></i>--}}
                    {{--<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" required/>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Email</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email" name="email" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" required/>
                </div>
            </div>
            <div>
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1"/> Remember me
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn green-haze btn-block">
                    Login <i class="m-icon-swapright m-icon-white"></i>
                </button>
            </div>

            {{--<div class="forget-password">--}}
                {{--<h5 class="text-right"><a href="#" id="forget-password"><strong>Forgot your password ?</strong></a></h5>--}}
            {{--</div>--}}
        </form>

        <form style="display: none;" class="forget-form" action="{{ url('/password/email') }}" method="post">
            {!! csrf_field() !!}
            <p>
                Enter your e-mail address below to reset your password.
            </p>
            <br>
            <br>
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
                </div>
            </div>
            <div class="form-group">
                <button type="button" id="back-btn" class="btn">
                    <i class="m-icon-swapleft"></i> Back </button>
                <button type="submit" class="btn green-haze pull-right">
                    Submit <i class="m-icon-swapright m-icon-white"></i>
                </button>
            </div>
        </form>
        <!-- END FORGOT PASSWORD FORM -->
    </div>

    <div class="copyright">
        {{ \Carbon\Carbon::now()->year }} &copy; <a href="{{ url('/') }}" target="_blank"> {{ ucwords(config('app.name')) }}</a>
    </div>
</div>
<!-- END LOGIN -->
<script src="{{ asset('plugins/respond.min.js') }}"></script>
<script src="{{ asset('plugins/excanvas.min.js') }}"></script>
<script src="{{ asset('plugins/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-migrate.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/jquery.blockui.min.js') }}"></script>
<script src="{{ asset('plugins/uniform/jquery.uniform.min.js') }}"></script>
<script src="{{ asset('plugins/jquery.cokie.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('scripts/metronic.js') }}"></script>
<script src="{{ asset('admin/layout4/scripts/layout.js') }}"></script>
<script src="{{ asset('admin/layout4/scripts/demo.js') }}"></script>
<script src="{{ asset('admin/pages/scripts/login.js') }}"></script>
<script>
    $('#forget-password').on('click', function (e) {
        e.preventDefault();
        $('.forget-form').removeAttr('style');
        $('.logindetail').attr('style', 'display:none');
    });
    $('#back-btn').on('click', function (e) {
        e.preventDefault();
        $('.logindetail').removeAttr('style');
        $('.forget-form').attr('style', 'display:none');
    });
</script>

</body>
<!-- END BODY -->
</html>