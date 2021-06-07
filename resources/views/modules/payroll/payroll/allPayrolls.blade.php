<style>
    .page-break {
        page-break-after: always;
    }

    @media print {
        .page-break {
            page-break-after: always;
        }
    }
</style>
@foreach($payrolls as $index => $payroll)
    @if($index)
        <br class="page-break">
    @endif
    @include('modules.payroll.payroll.payroll')
@endforeach
