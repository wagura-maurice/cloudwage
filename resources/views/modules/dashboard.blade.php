@extends('layout')

@section('header')
    <link rel="stylesheet" href="{{ asset('plugins/morris/morris.css') }}">
@endsection

@section('content')
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Home</h1>
        </div>
    </div>
    <!-- END PAGE HEAD -->
    <!-- BEGIN PAGE CONTENT INNER -->
    <div class="row margin-top-10 dashboard">
        <a href="/payroll/create" class="col-sm-3">
            <div class="dashboard-stat2">
                <div class="number">
                    <h3 class="font-green-sharp">{{ number_format($wages, 2) }}
                      <i class="pull-right fa fa-money fa-5x"></i>
                    </h3>
                    <small>WAGES DUE</small>
                </div>
            </div>
        </a>
        <a href="/employees" class="col-sm-3">
            <div class="dashboard-stat2">
                <div class="number">
                    <h3 class="font-green-sharp">{{ number_format($employees, 0) }}
                      <i class="pull-right fa fa-users fa-5x"></i>
                    </h3>
                    <small>EMPLOYEE{{ $employees == 1 ? '' : 'S'}}</small>
                </div>
            </div>
        </a>
        <a href="/departments" class="col-sm-3">
            <div class="dashboard-stat2">

                <div class="number">
                    <h3 class="font-green-sharp">{{ number_format($departments, 0) }}
                      <i class="fa fa-home fa-5x pull-right"></i>
                    </h3>
                    <small>DEPARTMENT{{ $departments == 1 ? '' : 'S' }}</small>
                </div>
            </div>
        </a>
        <a href="/loans" class="col-sm-3">
            <div class="dashboard-stat2">
                <div class="number">
                    <h3 class="font-green-sharp">{{ number_format($unpaidLoans, 0) }}
                      <i class="pull-right fa fa-credit-card fa-5x"></i>
                    </h3>
                    <small> UNPAID LOAN{{ $unpaidLoans == 1 ? '' : 'S' }}</small>
                </div>
            </div>
        </a>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">
                            Employee Loans Taken for Last 12 Months
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row list-separated text-center">
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Maximum Loan Amount
                            </div>
                            <div class="uppercase font-hg font-red-flamingo">
                                {{ number_format($maxLoan, 2) }}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Minimum Loan Amount
                            </div>
                            <div class="uppercase font-hg theme-font-color">
                                {{ number_format($minLoan, 2) }}
                            </div>
                        </div>
                    </div>
                    <div id="loans" class="chart-body-morris-fit morris-chart" style="height: 400px;">
                    </div>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>

        <div class="col-sm-6">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">
                            Employee Advances Taken for Last 12 Months
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row list-separated text-center">
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Maximum Advance Amount
                            </div>
                            <div class="uppercase font-hg font-red-flamingo">
                                {{ number_format($maxAdvance, 2) }}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Minimum Advance Amount
                            </div>
                            <div class="uppercase font-hg theme-font-color">
                                {{ number_format($minAdvance, 2) }}
                            </div>
                        </div>
                    </div>
                    <div id="advances" class="chart-body-morris-fit morris-chart" style="height: 400px;">
                    </div>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>

        <div class="col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <i class="icon-bar-chart theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">
                            Basic Pay for Last 12 Months
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row list-separated text-center">
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Maximum Total Gross
                            </div>
                            <div class="uppercase font-hg font-red-flamingo">
                                {{ number_format($maxBasic, 2) }}
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="font-grey-mint font-sm">
                                Minimum Total Gross
                            </div>
                            <div class="uppercase font-hg theme-font-color">
                                {{ number_format($minBasic, 2) }}
                            </div>
                        </div>
                    </div>
                    <div id="basic_pay" class="chart-body-morris-fit morris-chart" style="height: 400px;">
                    </div>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('plugins/amcharts/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/amcharts/serial.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/amcharts/themes/patterns.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/amcharts/themes/chalk.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/amcharts/themes/chalk.js') }}"></script>

    <script>
        $(document).ready(function () {
            AmCharts.makeChart( "basic_pay", {
                "type": "serial",
                "addClassNames": true,
                "theme": "light",
                "autoMargins": false,
                "marginLeft": 100,
                "marginRight": 20,
                "marginTop": 10,
                "marginBottom": 60,
                "balloon": {
                    "adjustBorderColor": false,
                    "horizontalPadding": 10,
                    "verticalPadding": 8,
                    "color": "#ffffff"
                },

                "dataProvider": [
                    @foreach($basicPay as $pay)
                    {
                        "date": "{{ $pay['date'] }}",
                        "pay": {{ $pay['pay'] }},
                        "min": {{ $minBasic }},
                        "max": {{ $maxBasic }}
                    },
                    @endforeach
                ],
                "startDuration": 1,
                "graphs": [
                    {
                        "alphaField": "alpha",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "fillAlphas": 1,
                        "title": "Pay",
                        "type": "column",
                        "valueField": "pay",
                        "dashLengthField": "dashLengthColumn"
                    },
                    {
                        "id": "graph2",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Min",
                        "valueField": "min",
                        "dashLengthField": "dashLengthLine"
                    },
                    {
                        "id": "graph3",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Max",
                        "valueField": "max",
                        "dashLengthField": "dashLengthLine"
                    }
                ],
                "categoryField": "date",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "tickLength": 0
                },
                "export": {
                    "enabled": true
                }
            });

            AmCharts.makeChart( "loans", {
                "type": "serial",
                "addClassNames": true,
                "theme": "light",
                "autoMargins": false,
                "marginLeft": 100,
                "marginRight": 20,
                "marginTop": 10,
                "marginBottom": 60,
                "balloon": {
                    "adjustBorderColor": false,
                    "horizontalPadding": 10,
                    "verticalPadding": 8,
                    "color": "#ffffff"
                },

                "dataProvider": [
                    @foreach($loans as $pay)
                    {
                        "date": "{{ $pay['date'] }}",
                        "pay": {{ $pay['loan_amount'] }},
                        "min": {{ $minLoan }},
                        "max": {{ $maxLoan }}
                    },
                    @endforeach
                ],
                "startDuration": 1,
                "graphs": [
                    {
                        "alphaField": "alpha",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "fillAlphas": 1,
                        "title": "Pay",
                        "type": "column",
                        "valueField": "pay",
                        "dashLengthField": "dashLengthColumn"
                    },
                    {
                        "id": "graph2",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Min",
                        "valueField": "min",
                        "dashLengthField": "dashLengthLine"
                    },
                    {
                        "id": "graph3",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Max",
                        "valueField": "max",
                        "dashLengthField": "dashLengthLine"
                    }
                ],
                "categoryField": "date",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "tickLength": 0
                },
                "export": {
                    "enabled": true
                }
            });

            AmCharts.makeChart( "advances", {
                "type": "serial",
                "addClassNames": true,
                "theme": "light",
                "autoMargins": false,
                "marginLeft": 100,
                "marginRight": 20,
                "marginTop": 10,
                "marginBottom": 60,
                "balloon": {
                    "adjustBorderColor": false,
                    "horizontalPadding": 10,
                    "verticalPadding": 8,
                    "color": "#ffffff"
                },

                "dataProvider": [
                    @foreach($advances as $pay)
                    {
                        "date": "{{ $pay['date'] }}",
                        "pay": {{ $pay['amount'] }},
                        "min": {{ $minAdvance }},
                        "max": {{ $maxAdvance }}
                    },
                    @endforeach
                ],
                "startDuration": 1,
                "graphs": [
                    {
                        "alphaField": "alpha",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "fillAlphas": 1,
                        "title": "Pay",
                        "type": "column",
                        "valueField": "pay",
                        "dashLengthField": "dashLengthColumn"
                    },
                    {
                        "id": "graph2",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Min",
                        "valueField": "min",
                        "dashLengthField": "dashLengthLine"
                    },
                    {
                        "id": "graph3",
                        "balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
                        "bullet": "round",
                        "lineThickness": 3,
                        "bulletSize": 7,
                        "bulletBorderAlpha": 1,
                        "bulletColor": "#FFFFFF",
                        "useLineColorForBulletBorder": true,
                        "bulletBorderThickness": 3,
                        "fillAlphas": 0,
                        "lineAlpha": 1,
                        "title": "Max",
                        "valueField": "max",
                        "dashLengthField": "dashLengthLine"
                    }
                ],
                "categoryField": "date",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "tickLength": 0
                },
                "export": {
                    "enabled": true
                }
            });
        });
    </script>
@endsection
