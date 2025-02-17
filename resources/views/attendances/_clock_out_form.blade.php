<form action="" id="clockOutForm" method="POST">
    @csrf

    <div class="form-group">
        <label for="employee_id">Employee</label>
        <select name="employee_id" id="employee_id" class="form-control" required>
            <option value="">-- Select Employee --</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="clock_out">Clock Out</label>
        <input type="time" name="clock_out" id="clock_out" class="form-control">
    </div>

    <div class="form-group">
        <label for="remarks">Remarks</label>
        <textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
    </div>

    <button type="button" onclick="clockOut(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle me-2"></i> Clock Out</button>

</form>
