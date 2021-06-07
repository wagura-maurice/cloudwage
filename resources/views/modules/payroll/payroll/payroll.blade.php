
<style>
    body {
        font-size: 0.8em;
        font-family: "helvetica", "Sans-Serif";
    }

    .s-table {
        line-height: 15px;
        width: 100%;
    }
    .s-table-header
    {
        background-color: #E5E5E5;
    }

    .s-table-header th {
        padding: 5px;
    }

    td {
        padding: 5px;
        border-bottom: solid #E5E5E5 1px;
    }
    .s-table-summary
    {
        background-color: #E5E5E5;
    }

    #header {
        height: 40px;
    }

    #intro {
        font-size: 1.2em;
        line-height: 18px;
    }

    .text-right
    {
        text-align: right;
    }

    .text-left
    {
        text-align: left;
    }

    .s-container {
        position:absolute;
        top: 0;
        left: 0;
        opacity:0.5;
        z-index:-10;
        width: 100%;
    }

    .s-container table {
        width: 100%;
    }

    .s-container td {
        border: none !important;
    }


    .bg-water {
        margin-top:50%;
        -webkit-transform: rotate(320deg);
        -moz-transform: rotate(320deg);
        -ms-transform: rotate(320deg);
        -o-transform: rotate(320deg);
        transform: rotate(320deg);
        font-size: 2em;
        text-align: center;
        opacity: 0.1;
        width: 120%;
    }

    .page-break
    {
        page-break-after: always;
    }
</style>

<div class="s-container">
    <table>
        <tr>
            <td>
                <div class="bg-water">{{ $company->name }}</div>
            </td>
            <td>
                <div class="bg-water">{{ $company->name }}</div>
            </td>
        </tr>
    </table>

</div>

<table class="s-table" id="header">
    <tr>
        <td>
            <div id="intro">
                <strong>Payslip</strong><br>
                <strong>{{ $company->name }} | {{ $company->city }}</strong>
            </div>
        </td>
        <td></td>
        <td>
            <div id="intro">
                <strong>Payslip</strong><br>
                <strong>{{ $company->name }} | {{ $company->city }}</strong>
            </div>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <div>
                <strong>{{ $payroll->employee->identification_type }}:</strong> {{ $payroll->employee->identification_number }}<br>
                <strong>Payroll No:</strong> {{ $payroll->employee->payroll_number }}<br>
                <strong>Full Name:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->middle_name }} {{ $payroll->employee->last_name }}<br>
                <strong>Date:</strong> {{ $payroll->payroll_date->startOfMonth()->toFormattedDateString() .' - '. $payroll->payroll_date->toFormattedDateString() }}<br>
            </div>
        </td>
        <td></td>
        <td>
            <div>
                <strong>{{ $payroll->employee->identification_type }}:</strong> {{ $payroll->employee->identification_number }}<br>
                <strong>Payroll No:</strong> {{ $payroll->employee->payroll_number }}<br>
                <strong>Full Name:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->middle_name }} {{ $payroll->employee->last_name }}<br>
                <strong>Date:</strong> {{ $payroll->payroll_date->startOfMonth()->toFormattedDateString() .' - '. $payroll->payroll_date->toFormattedDateString() }}<br>
            </div>
        </td>
        <td></td>
    </tr>
</table>
<?php
$totalDeductions = 0;
$totalBenefits = 0;
$totalAllowances = $payroll->basic_pay;
$allowances = json_decode($payroll->allowances);
$deductions = json_decode($payroll->deductions);
$untaxedAllowances = array();
?>

<table class="s-table">
    <tr class="s-table-header">
        <th class="text-left">Earnings</th>
        <th></th>
        <th class="text-left">Earnings</th>
        <th></th>
    </tr>
    <tr class="s-table-header">
        <th>Name</th>
        <th>Amount</th>
        <th>Name</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td>Basic Pay ({{ $payroll->for_rate }})</td>
        <td class="text-right">{{ number_format($payroll->basic_pay, 2) }}</td>
        <td>Basic Pay ({{ $payroll->for_rate }})</td>
        <td class="text-right">{{ number_format($payroll->basic_pay, 2) }}</td>
    </tr>

    @foreach($allowances as $allowance)
        @if($allowance->amount > 0 && $allowance->non_cash == 0)
            <?php $untaxedAllowances [] = $allowance; ?>
        @endif
        @if ($allowance->taxable)
            <?php $totalAllowances += $allowance->tax_amount; ?>
            <tr>
                <td>{{ $allowance->name }} {{ $allowance->amount > 0 ? 'Tax' : '' }}</td>
                <td class="text-right">{{ number_format($allowance->tax_amount, 2) }}</td>
                <td>{{ $allowance->name }} {{ $allowance->amount > 0 ? 'Tax' : '' }}</td>
                <td class="text-right">{{ number_format($allowance->tax_amount, 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr class="s-table-summary">
        <td><strong>Gross Pay</strong></td>
        <td class="text-right"><strong>{{ number_format($totalAllowances, 2) }}</strong></td>
        <td><strong>Gross Pay</strong></td>
        <td class="text-right"><strong>{{ number_format($totalAllowances, 2) }}</strong></td>
    </tr>

    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr class="s-table-header">
        <th class="text-left">Deductions</th>
        <th></th>
        <th class="text-left">Deductions</th>
        <th></th>
    </tr>
    @foreach(json_decode($payroll->deductions) as $deduction)
        @if(is_object($deduction->amount))
            <?php $totalDeductions += ($deduction->amount->amount - $deduction->amount->relief->amount); ?>
            <tr>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">{{ number_format($deduction->amount->amount, 2) }}</td>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">{{ number_format($deduction->amount->amount, 2) }}</td>
            </tr>
            <tr>
                <td>{{ $deduction->amount->relief->name }}</td>
                <td class="text-right">({{ number_format($deduction->amount->relief->amount, 2) }})</td>
                <td>{{ $deduction->amount->relief->name }}</td>
                <td class="text-right">({{ number_format($deduction->amount->relief->amount, 2) }})</td>
            </tr>
        @else
            <?php $totalDeductions += $deduction->amount; ?>
            <tr>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">{{ number_format($deduction->amount, 2) }}</td>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">{{ number_format($deduction->amount, 2) }}</td>
            </tr>
            @if ($deduction->name == "NSSF")
                <tr class="s-table-header">
                    <td><strong>Taxable Pay</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAllowances - $totalDeductions, 2) }}</strong></td>
                    <td><strong>Taxable Pay</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAllowances - $totalDeductions, 2) }}</strong></td>
                </tr>
            @endif
        @endif
    @endforeach
    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    @foreach(json_decode($payroll->advances) as $advance)
        <?php $totalDeductions += $advance->amount; ?>
        <tr>
            <td>{{ $advance->name }}</td>
            <td class="text-right">{{ number_format($advance->amount, 2) }}</td>
            <td>{{ $advance->name }}</td>
            <td class="text-right">{{ number_format($advance->amount, 2) }}</td>
        </tr>
    @endforeach
    @foreach(json_decode($payroll->loans) as $loan)
        <?php $totalDeductions += $loan->amount; ?>
        <tr>
            <td>{{ $loan->name }}</td>
            <td class="text-right">{{ number_format($loan->amount, 2) }}</td>
            <td>{{ $loan->name }}</td>
            <td class="text-right">{{ number_format($loan->amount, 2) }}</td>
        </tr>
    @endforeach
    <tr class="s-table-summary">
        <td><strong>Total Deductions</strong></td>
        <td class="text-right"><strong>{{ number_format($totalDeductions, 2) }}</strong></td>
        <td><strong>Total Deductions</strong></td>
        <td class="text-right"><strong>{{ number_format($totalDeductions, 2) }}</strong></td>
    </tr>

    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr class="s-table-header">
        <th class="text-left">Other Benefits</th>
        <th></th>
        <th class="text-left">Other Benefits</th>
        <th></th>
    </tr>
    @foreach($untaxedAllowances as $allowance)
        <?php $totalBenefits += $allowance->amount; ?>
        <tr>
            <td>{{ $allowance->name }}</td>
            <td class="text-right">{{ number_format($allowance->amount, 2) }}</td>
            <td>{{ $allowance->name }}</td>
            <td class="text-right">{{ number_format($allowance->amount, 2) }}</td>
        </tr>
    @endforeach
    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr class="s-table-header">
        <th class="text-left">Summary</th>
        <th></th>
        <th class="text-left">Summary</th>
        <th></th>
    </tr>
    <tr>
        <td><strong>Gross Pay</strong></td>
        <td class="text-right">{{ number_format($totalAllowances, 2) }}</td>
        <td><strong>Gross Pay</strong></td>
        <td class="text-right">{{ number_format($totalAllowances, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Deductions</strong></td>
        <td class="text-right">({{ number_format($totalDeductions, 2) }})</td>
        <td><strong>Deductions</strong></td>
        <td class="text-right">({{ number_format($totalDeductions, 2) }})</td>
    </tr>
    <tr>
        <td><strong>Other Benefits</strong></td>
        <td class="text-right">{{ number_format($totalBenefits, 2) }}</td>
        <td><strong>Other Benefits</strong></td>
        <td class="text-right">{{ number_format($totalBenefits, 2) }}</td>
    </tr>
    <tr class="s-table-summary">
        <td><strong>Net Pay</strong></td>
        <td class="text-right"><strong>{{ number_format(($totalAllowances - $totalDeductions) + $totalBenefits, 2) }}</strong></td>
        <td><strong>Net Pay</strong></td>
        <td class="text-right"><strong>{{ number_format(($totalAllowances - $totalDeductions) + $totalBenefits, 2) }}</strong></td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    @if ($policies->where('policy', Payroll\Models\Payroll::REMAINING_LEAVE_DAYS)->first()->value == 'true' && isset($leaves))
        <tr class="s-table-summary">
            <td><strong>Remaining Leave Days</strong></td>
            <td class="text-right"><strong>{{ $leaves }}</strong></td>
            <td><strong>Remaining Leave Days</strong></td>
            <td class="text-right"><strong>{{ $leaves }}</strong></td>
        </tr>
    @endif
    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr>
        <td><strong>Receiver's Signature</strong></td>
        <td></td>
        <td><strong>Receiver's Signature</strong></td>
        <td></td>
    </tr>
</table>
<span class="page-break"></span>
