<table class="table">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Clock In</th>
            <th>Clock Out</th>
            <th>Overtime Hours</th>
            <th>Remarks</th>
            <th>Logged By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $attendance->date }}</td>
                <td>{{ $attendance->clock_in ?? '-' }}</td>
                <td>{{ $attendance->clock_out ?? '-' }}</td>
                <td>{{ $attendance->overtime_hours }}</td>
                <td>{{ $attendance->remarks ?? '-' }}</td>
                <td>{{ $attendance->loggedBy->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
