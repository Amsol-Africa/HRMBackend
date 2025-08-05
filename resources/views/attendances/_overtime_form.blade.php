<form id="overtimeForm" method="POST">
    @csrf

    <div class="form-group">
        <label for="employee_id">Employee:</label>
        <select name="employee_id" id="employee_id" class="form-control" required>
            <option value="">Select Employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id? 'selected': '' }}>
                    {{ $employee->user->name?? 'N/A' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" class="form-control datepicker" placeholder="Date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
    </div>

    <div class="form-group">
        <label for="overtime_hours">Overtime Hours:</label>
        <input type="number" name="overtime_hours" id="overtime_hours" class="form-control" value="{{ old('overtime_hours') }}" required>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <button type="button" onclick="saveOvertime(this)" class="btn btn-primary"> <i class="bi bi-check-circle me-2"></i> Submit</button>
</form>
