<table class="table table-striped table-hover" id="attendancesTable">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Clock In</th>
            <th>Clock Out</th>
            <th>Overtime</th>
            <th>Remarks</th>
            <th>Logged By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
            <tr @if($attendance->is_absent) style="background-color: #FFE5B4;" @endif>
                <td>{{ $attendance->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $attendance->date->format("jS M Y") }}</td>
                <td>
                    @if($attendance->is_absent)
                        x Absent
                    @else
                        {{ $attendance->clock_in ?? '-' }}
                    @endif
                </td>
                <td>{{ $attendance->clock_out ?? '-' }}</td>
                <td>{{ $attendance->overtime_hours }} Hrs</td>
                <td>{{ $attendance->remarks ?? '-' }}</td>
                <td>{{ $attendance->loggedBy->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
