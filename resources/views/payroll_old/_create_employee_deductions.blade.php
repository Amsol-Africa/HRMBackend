<div>
    <h4>Manage Other Deductions</h4>

    <div class="row g-2 my-3">
        <div class="col-md-2">
            <button class="btn btn-primary w-100" onclick="addDeduction()">+ Add Employee Deduction</button>
        </div>
        <div class="col-md-2">
            <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#addDeductionModal">+ Add Deduction</button>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Deduction Name</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="deductionsTable">
            @foreach ($employee_deductions as $deduction)
                <tr id="row_{{ $deduction->id }}">
                    <td>
                        <select class="form-select" disabled>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $employee->id == $deduction->employee_id ? 'selected' : '' }}>
                                    {{ $employee->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="form-select" disabled>
                            @foreach ($deductions as $deduct)
                                <option value="{{ $deduct->id }}" {{ $deduct->id == $deduction->deduction_id ? 'selected' : '' }}>
                                    {{ $deduct->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control" value="{{ $deduction->amount }}" disabled></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editDeduction('{{ $deduction->id }}')">Edit</button>
                        <button class="btn btn-sm btn-success d-none" onclick="initSaveDeduction('{{ $deduction->id }}')">Save</button>
                        <button class="btn btn-sm btn-danger" onclick="removeDeduction('{{ $deduction->id }}')">Remove</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
