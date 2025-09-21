@php
    /** @var \App\Models\LeaveEntitlement $entitlement */
@endphp

<form id="leaveEntitlementsForm" method="POST" action="{{ route('leave-entitlements.update') }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="slug" value="{{ $entitlement->slug }}">

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
                    <dd class="col-7">
                        <input type="text" name="leave_type" value="{{ $entitlement->leaveType->name ?? '' }}" class="form-control">
                    </dt>
                    <dt class="col-5">Leave Period</dt>
                    <dd class="col-7">
                        <input type="text" name="leave_period" value="{{ $entitlement->leavePeriod->name ?? '' }}" class="form-control">
                    </dt>
                    <dt class="col-5">Entitled Days</dt>
                    <dd class="col-7">
                        <input type="number" name="entitled_days" value="{{ number_format((float)$entitlement->entitled_days, 2) }}" class="form-control" step="0.01">
                    </dt>
                    <dt class="col-5">Accrued Days</dt>
                    <dd class="col-7">
                        <input type="number" name="accrued_days" value="{{ number_format((float)($entitlement->accrued_days ?? 0), 2) }}" class="form-control" step="0.01">
                    </dt>
                </dl>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary" onclick="saveLeaveEntitlements(this)">Save</button>
        </div>
    </div>
</form>
