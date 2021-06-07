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
                                <div class="form-group form-md-line-input">
                                    <label for="personal_relief">Subtract personal relief</label>
                                    <select class="form-control" name="personal_relief" id="personal_relief">
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
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Calculate">
                                </div>
                            </div><div class="col-sm-6">
                                <div class="portlet light">
                                    <div class="portlet-body">
                                        <table class="table table-striped table-responsive">
                                            <tr>
                                                <th class="text-left">Gross Pay</th>
                                                <td class="text-right" id="gross_pay"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left">NSSF Contribution</th>
                                                <td class="text-right" id="ns_contribution"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Taxable Pay</th>
                                                <td class="text-right" id="taxable_pay"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Personal Relief</th>
                                                <td class="text-right" id="pRelief"></td>
                                            </tr>
                                            {{--<tr>--}}
                                            {{--<th class="text-right">Insurance Relief</th>--}}
                                            {{--<td class="text-right" id="insur_relief"></td>--}}
                                            {{--</tr>--}}
                                            <tr>
                                                <th class="text-left">PAYE</th>
                                                <td class="text-right" id="paye"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left">NHIF Contribution</th>
                                                <td class="text-right" id="nh_contribution"></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left">Net Pay</th>
                                                <td class="text-right" id="netPay"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
        var selector = function (something) {
            return document.querySelector(something);
        };

        function calculateNet() {
            var gross = parseFloat(selector('#gross_salary').value);
            // insurance = parseFloat($('#insurance_relief').val());

            var nssfAmount = calculateNSSF(gross);
            var taxablePay = gross - nssfAmount;
            var taxAmount = calculatePAYE(taxablePay);
            var afterTax = taxablePay - taxAmount;
            var relief = calculatePayeRelief();
            var nhifAmount = standardDeduction(gross);
            var net = (afterTax + relief) - nhifAmount;

            selector('#gross_pay').innerHTML = gross.toLocaleString();
            selector('#nh_contribution').innerHTML = nhifAmount.toLocaleString();
            selector('#paye').innerHTML = taxAmount.toLocaleString();
            selector('#ns_contribution').innerHTML = nssfAmount.toLocaleString();
            selector('#taxable_pay').innerHTML = taxablePay.toLocaleString();
            selector('#pRelief').innerHTML = relief.toLocaleString();
            selector('#netPay').innerHTML = net.toLocaleString();
        }

        function calculatePayeRelief() {
            if($('#personal_relief').val() === '1') return parseFloat(paye.relief);

            return 0;
        }

        function calculateNSSF(grossSalary) {
            if ($('#nssf_contribution').val() === '0') return 0;

            grossSalary = parseFloat(grossSalary);
            if (grossSalary < parseFloat(nssf.threshold)) return 0;
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

            return deduction;
        }

        function standardDeduction(grossSalary) {
            if($('#nhif_contribution').val() === '0') return 0;

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


        $('#calculator').on('submit', function (e) {
            e.preventDefault();
            calculateNet();
        });
    </script>
@endsection