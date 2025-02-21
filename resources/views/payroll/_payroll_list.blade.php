<div class="row g-3">
    @foreach ($payrolls as $payroll)
        <div class="col-md-8">
            @include('payroll._payroll_card', ['payroll' => $payroll])
        </div>
    @endforeach

    @if ($payrolls->isEmpty())
        <div class="col-md-12">
            <div class="card shadow-sm border border-secondary text-center p-4">
                <h5>No Payrolls Available</h5>
                <p>There are currently no payrolls to display for {{ $disp_location }}.</p>
            </div>
        </div>
    @endif
</div>
