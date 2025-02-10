<table class="table table-bordered table-hover" id="leaveEntitlementsTable">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Leave Type</th>
            <th>Leave Period</th>
            <th>Entitled Days</th>
            <th>Total Days</th>
            <th>Days Taken</th>
            <th>Days Remaining</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leaveEntitlements as $entitlement)
            <tr>
                <td>{{ $entitlement->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $entitlement->leaveType->name ?? 'N/A' }}</td>
                <td>{{ $entitlement->leavePeriod->name ?? 'N/A' }}</td>
                <td>{{ $entitlement->entitled_days }}</td>
                <td>{{ $entitlement->total_days }}</td>
                <td>{{ $entitlement->days_taken }}</td>
                <td>{{ $entitlement->days_remaining }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
