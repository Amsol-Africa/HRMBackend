<div class="table-responsive">
    <table id="employeesTable" class="table table-hover table-bordered w-100">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Department</th>
                <th>Location</th>
                <th>Basic Salary</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
            <tr data-employee-id="{{ $employee->id }}">
                <td>{{ $employee->user->name }}</td>
                <td>{{ $employee->employee_code }}</td>
                <td>{{ $employee->department ? $employee->department->name : 'N/A' }}</td>
                <td>{{ $employee->location ? $employee->location->name : 'Main Business' }}</td>
                <td>{{ $employee->paymentDetails->basic_salary ?? 'N/A' }}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewEmployee({{ $employee->id }})">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="editEmployee({{ $employee->id }})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEmployee({{ $employee->id }})">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No employees found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>