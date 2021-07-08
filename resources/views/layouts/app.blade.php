<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="payroll, kenya, nairobi, east africa, wages, paye, deductions, allowances, pay roll, cloud payments">
    <meta name="description" content="{{ ucwords(config('app.name')) }} is your number one payroll partner in Kenya and the world. Generate your payroll from anywhere in the world.">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
    <link rel="stylesheet" href="{{ asset('css/startup.css') }}">
    @yield('header')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body id="home">
    <nav class="n">
        <div class="nav-wrapper">
            <a href="{{ url('/') }}" class="brand-logo" style="height: 80%;margin-top: 5px;">
                <img src="{{ asset('/images/logo2.png') }}" alt="{{ ucwords(config('app.name')) }}" style="/**height: 100%**/">
            </a>
            <ul id="nav-mobile" class="ag hide-on-med-and-down">
                <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#home">Home</a></li>
                <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#features">Features</a></li>
                <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#buy">Buy Now!</a></li>
                <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#contact">Contact Us</a></li>
                <li><a class="waves-effect waves-teal" href="{{ url('/login') }}">Client Login</a></li>
            </ul>

            <a href="#" data-activates="slide-out" class="button-collapse ag"><i class="material-icons white-text">menu</i></a>
        </div>
    </nav>
    <ul id="slide-out" class="side-nav">
        <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#home">Home</a></li>
        <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#features">Features</a></li>
        <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#buy">Buy Now!</a></li>
        <li><a class="waves-effect waves-teal{{ Route::is('welcome') ? ' scroll' : '' }}" href="/#contact">Contact Us</a></li>
        <li><a class="waves-effect waves-teal" href="{{ url('/login') }}">Client Login</a></li>
    </ul>
    @yield('content')

    <footer class="page-footer">
        <div class="dv">
            <div class="c">
                <div class="d ka ko">
                    <a href="#" target="_blank">
                        <img class="materialize-logo" src="{{ asset('images/wizag.png') }}" alt="">
                    </a>
                    <p>&copy;{{ Carbon\Carbon::now()->year }} {{ ucwords(config('app.name')) }}</p>
                </div>
                <div class="d ka ko">
                    <h5>About</h5>
                    <ul>
                        <li><a href="#!">Blog</a></li>
                        <li><a href="#!">Other Products</a></li>
                        <li><a href="#!">Testimonials</a></li>
                    </ul>
                </div>
                <div class="d ka ko">
                    <h5>Connect</h5>
                    <ul>
                        <li><a href="#!">Subscribe</a></li>
                        <li><a href="#!">Support</a></li>
                        <li><a href="#!">Feedback</a></li>
                    </ul>
                </div>
                <div class="d ka ko">
                    <h5>Contact</h5>
                    <ul>
                        <li><a href="#!">Twitter</a></li>
                        <li><a href="#!">Facebook</a></li>
                        <li><a href="#!">Github</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js"></script>
    <script src="{{ asset('js/startup.js') }}"></script>
    <script>
        $(".scroll").off("click")
            .on("click", function (e) {
                e.preventDefault();
                var t = $(e.target.hash).offset().top;
                $("body").animate({scrollTop: t}, 1e3)
            });
    </script>
</body>
</html>
