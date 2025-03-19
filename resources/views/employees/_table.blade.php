<table class="table table-striped table-hover" id="employeesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Emp. Code</th>
            <th>ID. No.</th>
            <th>Location</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Job Title</th>
            <th>Sex</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $index => $employee)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $employee->employee_code }}</td>
            <td>{{ $employee->national_id }}</td>
            <td>
                {{ is_array($employee->location) || is_object($employee->location)
                        ? $employee->location->name ?? ($employee->business->company_name ?? 'N/A')
                        : $employee->business->company_name ?? 'N/A' }}
            </td>
            <td>{{ $employee->user->name }} </td>
            <td>{{ $employee->user->email }}</td>
            <td>{{ $employee->user->phone }}</td>
            <td>{{ $employee->department->name ?? 'N/A' }}</td>
            <td>{{ $employee->job_title ?? 'N/A' }}</td>
            <td>{{ strtoupper($employee->gender) }}</td>
            <td>
                <span class="badge
                        @if ($employee->status == 'active') bg-success
                        @elseif ($employee->status == 'on_leave') bg-warning
                        @elseif ($employee->status == 'notice_exit') bg-info
                        @elseif ($employee->status == 'inactive') bg-secondary
                        @elseif ($employee->status == 'exited') bg-danger
                        @else bg-light text-dark @endif">
                    {{ ucfirst($employee->status) }}
                </span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{-- route('employees.show', $employee->slug) --}}" class="btn btn-primary"
                        data-bs-toggle="tooltip" title="View Employee">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="btn btn-info edit-employee" onclick="editEmployee(this)"
                        data-employee="{{ $employee->slug }}" data-bs-toggle="tooltip" title="Edit Employee">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger delete-employee" onclick="deleteEmployee(this)"
                        data-employee="{{ $employee->slug }}" data-bs-toggle="tooltip" title="Delete Employee">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>