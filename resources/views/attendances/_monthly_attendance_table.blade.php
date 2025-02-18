<table class="table table-striped table-nowrap mb-0" id="attendancesTable">
    <thead>
        <tr>
            <th>Employee</th>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                <th>{{ $day }}</th>
            @endfor
        </tr>
    </thead>
    <tbody class="table__body">
        @foreach($attendanceData as $employeeId => $employeeAttendances)

            @php
            $employeeAttendancesCollection = collect($employeeAttendances);
            @endphp
            <tr>
                <td>
                    <span class="table-avatar">
                        <a class="employee__avatar mr-5" href="">
                            <img class="img-48 border-circle" src="{{ $employeeAttendancesCollection->first()?->employee->user->getImageUrl() ?? asset('default-avatar.png') }}" alt="User Image">
                        </a>
                        <a href="">{{ $employeeAttendancesCollection->first()?->employee->user->name ?? 'N/A' }}</a>
                    </span>
                </td>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    <td>
                        @if (isset($employeeAttendances[$day]))
                            @if ($employeeAttendances[$day]->is_absent)
                                <i class="fa fa-times text-danger"></i>
                            @else
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendance_info">
                                    <i class="fa fa-check text-success"></i>
                                </a>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
        @endforeach
    </tbody>
</table>
