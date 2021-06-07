@extends('layouts.app')

@section('content')
    <div class="gn ai full-height">
        <div class="header-wrapper c valign-wrapper">
            <div class="d jt offset-s2 ci">
                <button class="read-more"><i class="icon-caret-down"></i></button>
            </div>
        </div>
        <div class="laptop-preview-sizer">
            <div class="laptop-preview"></div>
            <div class="image-container et" style="background-image:url('/images/demo.jpg')"></div>
        </div>
    </div>

    <!-- Features -->
    <div class="v valign-wrapper" id="features">
        <div class="dv">
            <div class="c">
                <div class="d iv"><h2 class="section-title">Features</h2></div>
                <div class="d iv jo kk">
                    <h4><i class="icon-light-bulb"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
                <div class="d iv jo kk">
                    <h4><i class="icon-bolt"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
                <div class="d iv jo kk">
                    <h4><i class="icon-rocket"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
                <div class="d iv jo kk">
                    <h4><i class="icon-settings"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
                <div class="d iv jo kk">
                    <h4><i class="icon-umbrella"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
                <div class="d iv jo kk">
                    <h4><i class="icon-compass"></i></h4>
                    <p class="ek">This is a cool feature about your product! It really separates you from the crowd.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Tables -->
    <div class="v valign-wrapper" id="buy">
        <div class="dv">
            <div class="c">
                <div class="d iv"><h2 class="section-title">Buy Now!</h2></div>
                <div class="d iv kr">
                    <div class="pricing-table">
                        <div class="pricing-header">
                            <i class="icon-paper-plane"></i>
                            <h4>Basic</h4>
                            {{--<div class="gd">--}}
                                {{--<span class="hm">$</span>--}}
                                {{--<span class="dollars">9</span>--}}
                                {{--<span class="ic">99</span>--}}
                            {{--</div>--}}
                        </div>
                        <ul class="pricing-features">
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Pro and above.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                        </ul>
                        <div style="text-align: center">
                            <h5 style="font-style: italic;font-size: 12px">Free 30 Days Trial</h5>
                            <a href="{{ url('/register') }}" class="waves-effect waves-light btn" style="margin: 10px auto 40px auto;">Register Now</a>
                        </div>
                    </div>
                </div>

                <div class="d iv kr">
                    <div class="pricing-table fk">
                        <div class="pricing-header">
                            <i class="icon-plane"></i>
                            <h4>Pro</h4>
                            {{--<div class="gd">--}}
                                {{--<span class="hm">$</span>--}}
                                {{--<span class="dollars">59</span>--}}
                                {{--<span class="ic">99</span>--}}
                            {{--</div>--}}
                        </div>
                        <ul class="pricing-features">
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>Pro and above.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                            <li class="pricing-feature au"><i class="icon-close"></i>Enterprise only.</li>
                        </ul>
                        <div style="text-align: center">
                            <h5 style="font-style: italic;font-size: 12px">Free 30 Days Trial</h5>
                            <a href="{{ url('/register') }}" class="waves-effect waves-light btn" style="margin: 10px auto 40px auto;">Register Now</a>
                        </div>
                    </div>
                </div>

                <div class="d iv kr">
                    <div class="pricing-table">
                        <div class="pricing-header">
                            <i class="icon-rocket"></i>
                            <h4>Enterprise</h4>
                            {{--<div class="gd">--}}
                                {{--<span class="hm">$</span>--}}
                                {{--<span class="dollars">299</span>--}}
                                {{--<span class="ic">99</span>--}}
                            {{--</div>--}}
                        </div>
                        <ul class="pricing-features">
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>10 product uses.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>Enterprise only.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>Enterprise only.</li>
                            <li class="pricing-feature"><i class="icon-accept"></i>Enterprise only.</li>
                        </ul>
                        <div style="text-align: center">
                            <h5 style="font-style: italic;font-size: 12px">Free 30 Days Trial</h5>
                            <a href="{{ url('/register') }}" class="waves-effect waves-light btn" style="margin: 10px auto 40px auto;">Register Now</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            window.scrollTo(0, 450);
        })();
    </script>

@endsection