<table class="table table-hover table-bordered table-striped" id="leaveEntitlementsTable">
    <thead>
        <tr>
            <th>Employee No</th>
            <th>Employee</th>
            <th>Leave Type</th>
            <th>Entitled Days</th>
            <th>Total Days</th>
            <th>Days Taken</th>
            <th>Days <br> Remaining</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($leaveEntitlements as $entitlement)
            <tr>
                <td>
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-hash"></i>
                        {{ $entitlement->employee->employee_code ?? '—' }}
                    </a>
                </td>
                <td>{{ $entitlement->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $entitlement->leaveType->name ?? 'N/A' }}</td>
                <td>{{ number_format((float) $entitlement->entitled_days, 2) }}</td>
                <td>{{ number_format((float) $entitlement->total_days, 2) }}</td>
                <td>{{ number_format((float) $entitlement->days_taken, 2) }}</td>
                <td>{{ number_format((float) $entitlement->days_remaining, 2) }}</td>
                <td class="d-flex gap-1">
                    <a href="#" class="btn btn-danger btn-sm" title="Delete">
                        <i class="bi bi-trash"></i>
                    </a>
                    <a href="#" class="btn btn-secondary btn-sm" title="Details">
                        <i class="bi bi-view-list me-2"></i> Details
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted">No entitlements found for the selected period.</td>
            </tr>
        @endforelse
    </tbody>
</table>
