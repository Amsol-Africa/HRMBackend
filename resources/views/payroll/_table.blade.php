@forelse($employees as $employee)
<tr>
    <td>
        <input type="checkbox" name="exempted_employees[{{ $employee->id }}]" value="1"
            onchange="updateExemptedEmployees()" class="employee-checkbox" data-employee-id="{{ $employee->id }}"
            checked>
    </td>
    <td>{{ $employee->user?->name ?? 'N/A' }}</td>
    <td>{{ $employee->employee_code ?? 'N/A' }}</td>
    <td>{{ $employee->location?->name ?? $business->company_name }}</td>
    <td>{{ $employee->employmentDetails?->department?->name ?? 'N/A' }}</td>
    <td>{{ $employee->employmentDetails?->jobCategory?->name ?? 'N/A' }}</td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center">No employees found</td>
</tr>
@endforelse