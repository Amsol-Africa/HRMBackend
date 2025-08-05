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
        @foreach ($leaveEntitlements as $entitlement)
            <tr>
                <td>
                    <a href="" class="btn btn-sm btn-outline-primary"> <i class="bi bi-hash"></i> {{ $entitlement->employee->employee_code }}</a>
                </td>
                <td>{{ $entitlement->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $entitlement->leaveType->name ?? 'N/A' }}</td>
                <td>{{ number_format($entitlement->entitled_days) }}</td>
                <td>{{ number_format($entitlement->total_days) }}</td>
                <td>{{ number_format($entitlement->days_taken) }}</td>
                <td>{{ number_format($entitlement->days_remaining) }}</td>
                <td>
                    <a href="" class="btn btn-danger btn-sm"> <i class="bi bi-trash"></i> </a>
                    <a href="" class="btn btn-secondary btn-sm"> <i class="bi bi-view-list me-2"></i> Details </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
