@php
    /** @var \App\Models\LeaveEntitlement $entitlement */
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="border rounded p-3 h-100">
            <h6 class="text-muted mb-3">Employee</h6>
            <dl class="row mb-0">
                <dt class="col-5">Name</dt>
                <dd class="col-7">{{ $entitlement->employee->user->name ?? '—' }}</dd>

                <dt class="col-5">Employee Code</dt>
                <dd class="col-7">{{ $entitlement->employee->employee_code ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-md-6">
        <div class="border rounded p-3 h-100">
            <h6 class="text-muted mb-3">Entitlement</h6>
            <dl class="row mb-0">
                <dt class="col-5">Leave Type</dt>
                <dd class="col-7">{{ $entitlement->leaveType->name ?? '—' }}</dd>

                <dt class="col-5">Leave Period</dt>
                <dd class="col-7">{{ $entitlement->leavePeriod->name ?? '—' }}</dd>

                <dt class="col-5">Entitled Days</dt>
                <dd class="col-7">{{ number_format((float)$entitlement->entitled_days, 2) }}</dd>

                <dt class="col-5">Accrued Days</dt>
                <dd class="col-7">{{ number_format((float)($entitlement->accrued_days ?? 0), 2) }}</dd>

                <dt class="col-5">Total Days</dt>
                <dd class="col-7">{{ number_format((float)$entitlement->total_days, 2) }}</dd>

                <dt class="col-5">Days Taken</dt>
                <dd class="col-7">{{ number_format((float)$entitlement->days_taken, 2) }}</dd>

                <dt class="col-5">Days Remaining</dt>
                <dd class="col-7">{{ number_format((float)$entitlement->days_remaining, 2) }}</dd>
            </dl>
        </div>
    </div>
</div>
