<table class="table table-striped table-hover" id="employeesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Emp. Code</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Job Title</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $index => $employee)
            @php
                echo '<pre>';
                var_dump($employees);
                echo '</pre>';
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $employee->employee_code }}</td>
                <td>{{ $employees->name }} </td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                <td>{{ $employee->jobTitle->name ?? 'N/A' }}</td>
                <td>
                    @if ($employee->status == 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif ($employee->status == 'on_leave')
                        <span class="badge bg-warning">On Leave</span>
                    @elseif ($employee->status == 'notice_exit')
                        <span class="badge bg-info">Notice of Exit</span>
                    @elseif ($employee->status == 'inactive')
                        <span class="badge bg-secondary">Inactive</span>
                    @elseif ($employee->status == 'exited')
                        <span class="badge bg-danger">Exited</span>
                    @else
                        <span class="badge bg-light text-dark">{{ ucfirst($employee->status) }}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{-- route('employees.show', $employee->slug) --}}" class="btn btn-primary" data-bs-toggle="tooltip"
                            title="View Employee">
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
