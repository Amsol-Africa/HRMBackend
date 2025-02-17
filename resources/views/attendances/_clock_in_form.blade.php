<form action="" id="clockInForm" method="POST">
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

    <!-- Hidden Date (Auto-filled) -->
    <input type="hidden" name="date" id="date" value="{{ now()->format('Y-m-d') }}">

    <div class="form-group">
        <label>
            <input type="checkbox" name="is_absent" value="1" id="is_absent" onchange="toggleClockFields()"> Mark as Absent
        </label>
    </div>

    <div class="form-group">
        <label for="clock_in">Clock In</label>
        <input type="time" name="clock_in" id="clock_in" value="{{ now()->format("H:i") }}" class="form-control">
    </div>

    <div class="form-group">
        <label for="remarks">Remarks</label>
        <textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
    </div>

    <button type="button" onclick="clockIn(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle me-2"></i> Clock In</button>

</form>
