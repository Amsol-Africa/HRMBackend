<div class="card h-100">
    <div class="card-header border-0">
        <h5 class="card__heading-title">

            <?php
                $startDate = date("F", strtotime($payroll->start_date));
                $endDate = date("F", strtotime($payroll->end_date));

                if ($startDate == $endDate) {
                    echo '<i class="fa-solid fa-wallet"></i> Payroll for ' . $startDate . ' ' . date("Y", strtotime($payroll->start_date));
                } else {
                    echo '<i class="fa-solid fa-wallet"></i> Payroll from this ' . $startDate . ' to this ' . $endDate . ', ' . date("Y", strtotime($payroll->start_date)) .
                        (date("Y", strtotime($payroll->end_date)) != date("Y", strtotime($payroll->start_date)) ? ', ' . date("Y", strtotime($payroll->end_date)) : '');
                }
            ?>

            @if ($payroll->employeePayrolls()->count() > 0)
            <span class="badge bg-success">PROCESSED</span>
            @else
            <span class="badge bg-warning">READY</span>
            @endif
        </h5>
    </div>

    <div class="card-body">
        <div class="row g-2" style="margin-bottom: 30px;">
            <div class="col-md-4">
                <h6 class="card-title">NO. OF EMPLOYEES</h6>
                <p><strong>{{ $payroll->staff }}</strong></p>
            </div>
            <div class="col-md-4">
                <h6 class="card-title">PAYMENT DATE</h6>
                <p><strong>{{ date("d, F Y", strtotime($payroll->end_date)) }}</strong></p>
            </div>
            <div class="col-md-4">
                <h6 class="card-title">TOTAL NET PAY</h6>
                <p>
                    @if ($payroll->employeePayrolls()->count() > 0)
                        <a href="{{ route('business.payroll.payslips', ['business' => $currentBusiness->slug, 'payroll' => $payroll->id]) }}" class="btn btn-info">View Payslips</a>
                    @else
                        <span class="text-muted">Not Processed</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-md-12">
                @if ($payroll->employeePayrolls()->count() > 0)
                    <button class="btn btn-secondary btn-sm w-100" disabled>
                        <i class="fa-solid fa-check"></i> Payroll Already Processed
                    </button>
                @else
                    <a href="#" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-arrow-right"></i> Run Payroll
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>
