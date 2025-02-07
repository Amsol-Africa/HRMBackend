<div class="row g-3">
    @foreach($payrolls as $payroll)
    <div class="col-md-8">
        @include('payroll._payroll_card', ['payroll' => $payroll])
    </div>
    @endforeach
</div>
