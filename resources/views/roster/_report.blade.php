<div class="card shadow-sm border-0">
    <div class="card-header bg-light">
        <h5 class="mb-0">Roster Report</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>Filters Applied:</strong>
            <ul class="list-unstyled">
                <li>Department:
                    {{ $filters['department_id'] ? App\Models\Department::find($filters['department_id'])->name : 'All' }}
                </li>
                <li>Job Category:
                    {{ $filters['job_category_id'] ? App\Models\JobCategory::find($filters['job_category_id'])->name : 'All' }}
                </li>
                <li>Location:
                    {{ $filters['location_id'] ? App\Models\Location::find($filters['location_id'])->name : 'All' }}
                </li>
                <li>Employee:
                    {{ $filters['employee_id'] ? App\Models\Employee::find($filters['employee_id'])->user->first_name . ' ' . App\Models\Employee::find($filters['employee_id'])->user->last_name : 'All' }}
                </li>
            </ul>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Roster</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Job Category</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Leave</th>
                        <th>Overtime (Hrs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rosters as $roster)
                    @foreach ($roster->assignments as $assignment)
                    <tr>
                        <td>{{ $roster->name }}</td>
                        <td>{{ $assignment->employee->user->first_name }} {{ $assignment->employee->user->last_name }}
                        </td>
                        <td>{{ $assignment->department->name }}</td>
                        <td>{{ $assignment->jobCategory->name }}</td>
                        <td>{{ $assignment->location->name }}</td>
                        <td>{{ $assignment->date->format('Y-m-d') }}</td>
                        <td>{{ $assignment->shift ? $assignment->shift->name : 'N/A' }}</td>
                        <td>{{ $assignment->leave ? $assignment->leave->name : 'N/A' }}</td>
                        <td>{{ number_format($assignment->overtime_hours, 2) }}</td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-end">Total Overtime Hours:</th>
                        <th>{{ number_format($rosters->flatMap->assignments->sum('overtime_hours'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>