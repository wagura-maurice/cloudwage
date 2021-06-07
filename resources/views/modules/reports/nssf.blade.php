<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table>
                <thead>
                <tr>
                    <td>Nssf Contribution</td>
                </tr>
                <tr>
                    <td>Employer Number</td>
                    <td>{{ $company->nssf }}</td>
                </tr>
                <tr>
                    <td>Employer Name</td>
                    <td>{{ $company->name }}</td>
                </tr>
                <tr>
                    <td>Period</td>
                    <td>{{ $payroll_date }}</td>
                </tr>
                <tr>
                    <th>Payroll Number</th>
                    <th>Employee Name</th>
                    <th>Member Number</th>
                    <th>Standard Amount</th>
                    <th>Voluntary Amount</th>
                    <th>Total</th>
                    <th>ID No</th>
                    <th>Remarks</th>
                </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    @foreach($payrolls as $payroll)
                        <?php $total += floatval($payroll->deductions->amount); ?>
                <tr>
                    <td>{{ $payroll->employee->payroll_number }}</td>
                    <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                    <td>{{ $payroll->employee->deductions->first()->deduction_number }}</td>
                    <td>{{ $payroll->deductions->amount }}</td>
                    <td>{{ 0 }}</td>
                    <td>{{ $payroll->deductions->amount }}</td>
                    <td>{{ $payroll->employee->identification_number }}</td>
                    <td></td>
                </tr>
                    @endforeach
                    <tr></tr>
                <tr>
                    <td></td>
                    <td>TOTALS</td>
                    <td></td>
                    <td>{{ $total }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>