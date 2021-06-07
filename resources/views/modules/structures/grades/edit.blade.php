@extends('layout')
@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Pay Grades - <small> Set up the pay grades that will be assigned within the organization</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('grades.index') }}">Pay Grades</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="#">Edit</a>
        </li>
    </ul>

    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('grades.update', $grade->id) }}" method="post" role="form">
                {{ csrf_field() }}
                {{ method_field('put') }}
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <i class="fa fa-briefcase font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> Pay Grade Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="text" class="form-control" id="name" name="name" value="{{ $grade->name or old('name') }}" required>
                                <label for="name">Pay Grade Name* (e.g. Group A)</label>
                                <span class="help-block">This is the name of the pay grade</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <select class="form-control" id="payment_structure_id" name="payment_structure_id">
                                    @foreach($payment_structures as $structure)
                                        <option value="{{ $structure->id }}" {{ $structure->id == $grade->payment_structure_id ? 'selected' : '' }}>{{ $structure->name }}</option>
                                    @endforeach
                                </select>
                                <label for="payment_structure_id">Payment Structure*</label>
                                <span class="help-block">This will be the applicable payment structure that will be used with this grade</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <select class="form-control" id="currency_id" name="currency_id">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ $currency->id == $grade->currency_id ? 'selected' : '' }}>{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                                <label for="name">Currency*</label>
                                <span class="help-block">This is the currency to be used within the pay grade</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="text" class="form-control" id="basic_salary" name="basic_salary" value="{{ $grade->basic_salary or old('basic_salary') }}" required>
                                <label for="annual_increment">Salary PER <span id="unit" class="uppercase"></span>*</label>
                                <span class="help-block">This is the basic salary for the job group</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="text" class="form-control" id="annual_increment" name="annual_increment" value="{{ $grade->annual_increment or old('annual_increment') }}" required>
                                <label for="annual_increment">Annual Increment* (e.g. 10%)</label>
                                <span class="help-block">This is the expected annual increment of the basic salary</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <select name="advance_for_future" id="advance_for_future" class="form-control">
                                    <option value="0"{{ $grade->advance_for_future == 0 ? ' selected' : '' }}>No</option>
                                    <option value="1"{{ $grade->advance_for_future == 1 ? ' selected' : '' }}>Yes</option>
                                </select>
                                <label for="">Advance For Future</label>
                                <span class="help-block">This is advance given in the future</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <select name="multiple_advances" id="multiple_advances" class="form-control">
                                    <option value="0"{{ $grade->multiple_advances == 0 ? ' selected' : '' }}>No</option>
                                    <option value="1"{{ $grade->multiple_advances == 1 ? ' selected' : '' }}>Yes</option>
                                </select>
                                <label for="">Multiple Advances</label>
                                <span class="help-block">These are multiple advances given</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <select name="advance_basic" id="advance_basic" class="form-control">
                                    <option value="0"{{ $grade->advance_basic == 0 ? ' selected' : '' }}>No</option>
                                    <option value="1"{{ $grade->advance_basic == 1 ? ' selected' : '' }}>Yes</option>
                                </select>
                                <label for="advance_basic">Advance Basic</label>
                                <span class="help-block">This is advance given more than the basic salary</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="text" name="gross_percentage" id="gross_percentage" class="form-control" value="{{ $grade->gross_percentage or old('gross_percentage') }}">
                                <label for="">Gross Percentage as Advance(%)</label>
                                <span class="help-block">This is the percentage of the gross</span>
                            </div>
                            <div class="form-group form-md-line-input">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="name">Default Allowances*</label><br><br>
                                @if($grade->default_allowances)
                                    <?php $default_allowances = explode(",", $grade->default_allowances); ?>
                                    @foreach($allowances as $allowance)
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-9 control-label">{{ $allowance->name }}</label>
                                            <div class="col-sm-3">
                                                <input type="checkbox" name="default_allowances[]" {{ in_array($allowance->id, $default_allowances) ? 'checked' : '' }} class="make-switch" value="{{ $allowance->id }}" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($allowances as $allowance)
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-9 control-label">{{ $allowance->name }}</label>
                                            <div class="col-sm-3">
                                                <input type="checkbox" name="default_allowances[]" class="make-switch" value="{{ $allowance->id }}" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                        <br><br><br>
                                        <p>These will be the default allowances that will be used with this grade</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="name">Default Deductions*</label><br><br>
                                        @if($grade->default_deductions)
                                            <?php $default_deductions = explode(",", $grade->default_deductions); ?>
                                            @foreach($deductions as $deduction)
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-sm-9 control-label">{{ $deduction->name }}</label>
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" name="default_deductions[]" {{ in_array($deduction->id, $default_deductions) ? 'checked' : '' }} class="make-switch" value="{{ $deduction->id }}" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            @foreach($deductions as $deduction)
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-sm-9 control-label">{{ $deduction->name }}</label>
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" name="default_deductions[]" class="make-switch" value="{{ $deduction->id }}" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times'></i>">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <br><br><br>
                                        <p>These will be the default deductions that will be used with this grade</p>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="submit" class="btn btn-primary" value="Save">
                                <a class="btn btn-danger" href="{{ route('grades.index') }}">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('footer')
    <script>
        var structures = {!! Payroll\Models\PaymentStructure::get(['id', 'unit'])->toJson() !!}
        var unitElem = $('#unit');
        var structElem = $('#payment_structure_id');

        function setUnit(elem) {
            var selectedStructure = elem.val();

            $.each(structures, function(i, v) {
                if (v.id == selectedStructure) {
                    unitElem.html(v.unit);
                }
            });
        }

        structElem.on('change', function () {
            setUnit($(this));
        });

        setUnit(structElem);
    </script>
@stop