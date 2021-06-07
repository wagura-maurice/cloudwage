@extends('layout')
@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Calculator - <small>This page calculates paye</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="#">Calculator</a>
        </li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-briefcase font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Paye Calculator</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <form id="calculator" method="post">
                            <div class="col-sm-6">
                                {{--<div class="form-group form-md-line-input">--}}
                                    {{--<label for="">Tax Year*</label>--}}
                                    {{--<br>--}}
                                    {{--<input type="radio" class="form-control" name="payroll_date" id="payroll_date">--}}
                                {{--</div>--}}
                                {{--<div class="form-group form-md-line-input">--}}
                                    {{--<label for="payroll_date">Pay Period</label>--}}
                                    {{--<br>--}}
                                    {{--<input type="radio" class="form-control" id="pay_period">Month--}}
                                    {{--<input type="radio" class="form-control" id="payroll_date">Year--}}
                                {{--</div>--}}
                                <div class="form-group form-md-line-input">
                                    <label for="">Gross Salary</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" id="gross_salary" required>
                                        <span class="input-group-btn">
                                    <span class="btn btn-sm default">{{ Payroll\Models\CompanyProfile::first()->currency->code }}</span>
                                </span>
                                    </div>
                                </div>
                                {{--<div class="form-group form-md-line-input form-md-floating-label">--}}
                                    {{--<input type="number" min="0" class="form-control" name="" id="insurance_relief">--}}
                                    {{--<label for="">Insurance Relief</label>--}}
                                    {{--<span class="help-block">This is the insurance relief</span>--}}
                                {{--</div>--}}
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group form-md-line-input">
                                    <label for="personal_relief">Subtract personal relief</label>
                                    <select class="form-control" name="personal_relief" id="personal_relief" onChange="personalReliefToggle()">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group form-md-line-input">
                                    <label for="nssf_contribution">Subtract NSSF contribution</label>
                                    <select class="form-control" name="nssf_contribution" id="nssf_contribution">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group form-md-line-input">
                                    <label for="nhif_contribution">Subtract NHIF contribution</label>
                                    <select class="form-control" name="nhif_contribution" id="nhif_contribution">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Calculate">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="portlet light">
                <div class="portlet-body">
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th>Gross Pay</th>
                            <th>NSSF Contribution</th>
                            <th>Taxable Pay</th>
                            <th>Personal Relief</th>
                            {{--<th>Insurance Relief</th>--}}
                            <th>PAYE</th>
                            <th>NHIF Contribution</th>
                            <th>Net Pay</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-right" id="gross_pay"></td>
                            <td class="text-right" id="ns_contribution"></td>
                            <td class="text-right" id="taxable_pay"></td>
                            <td class="text-right" id="pRelief"></td>
                            {{--<td class="text-right" id="insur_relief"></td>--}}
                            <td class="text-right" id="paye"></td>
                            <td class="text-right" id="nh_contribution"></td>
                            <td class="text-right"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection
@section('footer')
    <script>
        var deductions = {!! json_encode($deduction) !!};
        var paye = deductions[1];
        var nhif = deductions[2];
        var nssf = deductions[3];
        var grossSalary = $('#gross_pay');
        var nhifContribution = $('#nh_contribution');
        var payeContribution = $('#paye');
        var nssfContribution = $('#ns_contribution');
        var taxable = $('#taxable_pay');


        $('#calculator').on('submit', function (e) {
            e.preventDefault();
            calculateNet();
        });

        function personalReliefToggle(e) {
            if($('#personal_relief').val) {
                $('#pRelief').val = 10;
            } else {
                $('#pRelief').val = 0;
            }
        }

        function calculateNet() {
            var gross = parseFloat($('#gross_salary').val());
//            var insurance = parseFloat($('#insurance_relief').val());
            var hasRelief = $('#personal_relief').val() === '1';
            var hasNSSF = $('#nssf_contribution').val() === '1';
            var hasNHIF = $('#nhif_contribution').val() === '1';
            var taxablePay = gross;
            var nssf = 0;
            var nhif = 0;
            var taxAmount = 0;
            var net = 0;
            var afterTax = taxablePay;
            var personalRelief = $('#pRelief');


            grossSalary.on('change', function (e) {
                return gross;
            });

            if (hasNSSF) nssf = calculateNSSF(gross);
            taxablePay -= nssf;

            nssfContribution.on('change', function (e) {
                calculateNSSF(gross);
            });

            taxable.on('change', function (e) {
                return parseFloat(gross - nssf);
            });

//            console.log(parseFloat(gross - nssf));
            taxAmount = calculatePAYE(taxablePay);
            afterTax -= taxAmount;

            payeContribution.on('change', function (e) {
                calculatePAYE(taxablePay)
            });

            if(hasRelief) {
                afterTax += parseFloat(paye.relief);

                personalRelief.on('change', function (e) {
                    if(e.value) {
                        $('#pRelief').val = paye.relief;
                    } else {
                        $('#pRelief').val = 0;
                    }
                    updatePRelief(e.target.value == 1);
                });
            }


            if(hasNHIF) {
                nhif = standardDeduction(afterTax);

                nhifContribution.on('change', function (e) {
                    standardDeduction(gross);
                });
            }

            console.log(afterTax);
        }

        function calculateNSSF(grossSalary) {
            grossSalary = parseFloat(grossSalary);
            if (grossSalary < nssf.threshold) return 0;
            if (nssf.type === 'rate') return parseFloat(nssf.rate);
            var slabs = nssf.slabs;
            var lowerLimit = parseFloat(slabs[1].max_amount);
            var upperLimit = parseFloat(slabs[2].max_amount);

            if(grossSalary <= lowerLimit) {
                return (grossSalary * parseFloat(nssf.slabs.rate)) / 100;
            }

            var tier1 = (lowerLimit * slabs[1].rate) / 100;
            if(grossSalary <= upperLimit) {
                return tier1 + (((grossSalary - lowerLimit) * nssf.slabs.rate)/100);
            }

            var tier2 = (upperLimit - lowerLimit) * slabs[2].rate;

            var result = tier1 + (tier2 / 100);

            document.querySelector('#ns_contribution').innerHTML = result;

            return tier1 + (tier2 / 100);
        }

        function calculatePAYE(grossSalary) {
            var slabs = Object.values(paye.slabs);
            var threshold = paye.threshold;
            var deduction = 0;

            if (grossSalary < threshold) return deduction;

            for (var i = 0; i < slabs.length; i++) {
                var lowerLimit = slabs[i].min_amount == 0 ? 0 : parseFloat(slabs[i].min_amount) - 1;
                var upperLimit = parseFloat(slabs[i].max_amount);
                var rate = parseFloat(slabs[i].rate) / 100;

                if (upperLimit === 0) {
                    deduction += (grossSalary - (lowerLimit + 1)) * rate;
                    continue;
                }

                if (grossSalary >= upperLimit) {
                    deduction += (upperLimit - lowerLimit) * rate;
                    continue;
                }

                deduction += (grossSalary - lowerLimit) * rate;
                break;
            }

            document.querySelector('#paye').innerHTML = deduction;

            return deduction;
        }

        function standardDeduction(grossSalary) {
            console.log(grossSalary);
            if (nhif.type !== 'slab') {
                if (nhif.rate.indexOf('%') !== -1) {
                    return grossSalary * (parseFloat(nhif.rate.substr(0, nhif.rate.length -1)) / 100)
                }

                return parseFloat(nhif.rate);
            }

            var slab = Object.values(nhif.slabs).filter(function (slab) {
                var max = parseFloat(slab.max_amount);
                var min = parseFloat(slab.min_amount);

                var isLast = grossSalary <= max;
                if (! max) isLast = true;

                return grossSalary >= min && isLast;
            });

            if (! slab.length) return 0;

            var result = parseFloat(slab[0].rate);

            document.querySelector('#nh_contribution').innerHTML = result;

            return parseFloat(slab[0].rate);
        }


//        var personalRelief = $('#personal_relief');
//
//        var payPeriod = $('#pay_period');
//        var grossSalary = $('#gross_salary');
////        var insuranceRelief = $('#insurance_relief');
//        var nssfContribution = $('#nssf_contribution');
//        var nhifContribution = $('#nhif_contribution');
//


        function updatePRelief(enabled) {
            var rel = paye.relief;

            if (enabled) {
                rel = parseInt(paye.relief);
                rel = isNaN(rel) ? 0 : rel.toLocaleString();
            }
            document.querySelector('#pRelief').innerHTML = rel;
            console.log(paye.relief);
        }

        updatePRelief(true);

        grossSalary.on('keyup', function(e){
            var value = parseInt(e.target.value);

            value = isNaN(value) ? 0 : value.toLocaleString();

            document.querySelector('#gross_pay').innerHTML = value;
        });

        function getGrossSalary() {
            grossSalary.on('change', function (e) {
                var gross = parseFloat(e.target.value);

                gross = isNaN(gross) ? 0 : gross.toLocaleString();

                document.querySelector('#gross_pay').innerHTML = gross;
            })
        }
//
//        nssfContribution.on('change', function (e) {
//            calculateNSSF(e.target.value);
//        });
//
//        document.querySelector('#ns_contribution').innerHTML = total;
//
//        insuranceRelief.on('keyup', function(e){
//            var reliefValue = parseInt(e.target.value);
//
//            reliefValue = isNaN(reliefValue) ? 0 : reliefValue.toLocaleString();
//
//            document.querySelector('#insur_relief').innerHTML = reliefValue;
//        });


    </script>
    @endsection