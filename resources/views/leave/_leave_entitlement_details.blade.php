<div class="mb-2"><strong>Employee:</strong> {{ $entitlement->employee->user->name ?? 'N/A' }}</div>
<div class="mb-2"><strong>Employee No:</strong> {{ $entitlement->employee->employee_code ?? '—' }}</div>
<div class="mb-2"><strong>Leave Type:</strong> {{ $entitlement->leaveType->name ?? 'N/A' }}</div>
<div class="mb-2"><strong>Period:</strong> {{ $entitlement->leavePeriod->name ?? 'N/A' }}</div>
<hr>
<div class="row g-3">
    <div class="col-6"><strong>Entitled Days:</strong> {{ number_format((float)$entitlement->entitled_days,2) }}</div>
    <div class="col-6"><strong>Accrued Days:</strong> {{ number_format((float)$entitlement->accrued_days,2) }}</div>
    <div class="col-6"><strong>Total Days:</strong> {{ number_format((float)$entitlement->total_days,2) }}</div>
    <div class="col-6"><strong>Days Taken:</strong> {{ number_format((float)$entitlement->days_taken,2) }}</div>
    <div class="col-6"><strong>Days Remaining:</strong> {{ number_format((float)$entitlement->days_remaining,2) }}</div>
</div>
