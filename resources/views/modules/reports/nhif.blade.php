<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <table>
                <thead>
                <tr>
                    <td>EMPLOYER CODE </td>
                    <td>{{ $company->nhif }} </td>
                </tr>
                <tr>
                    <td>EMPLOYER NAME</td>
                    <td>{{ $company->name }}</td>
                </tr>
                <tr>
                    <td>MONTH OF CONTRIBUTION</td>
                    <td>{{ $payroll_date }}</td>
                </tr>
                <tr>
                    <th>PAYROLL NUMBER</th>
                    <th>LAST NAME</th>
                    <th>FIRST NAME</th>
                    <th>ID NUMBER</th>
                    <th>NHIF NUMBER</th>
                    <th>AMOUNT</th>
                </tr>
                </thead>
                <tbody>
                <?php $total = 0; ?>
                @foreach($payrolls as $payroll)
                    <?php $total += floatval($payroll->deductions->amount); ?>
                    <tr>
                        <td>{{ $payroll->employee->payroll_number }}</td>
                        <td>{{ $payroll->employee->last_name }}</td>
                        <td>{{ $payroll->employee->first_name }}</td>
                        <td>{{ $payroll->employee->identification_number }}</td>
                        <td>{{ $payroll->employee->deductions->first()->deduction_number }}</td>
                        <td>{{ $payroll->deductions->amount }}</td>
                    </tr>
                @endforeach
                <tr></tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td rowspan="2">TOTAL</td>
                    <td>{{ $total }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>