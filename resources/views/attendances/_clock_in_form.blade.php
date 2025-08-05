<form action="" id="clockInForm" method="POST">
    @csrf

    @if (auth()->user()->hasRole('employee') && str_contains(request()->url(), 'myaccount'))
    <input type="hidden" name="employee_id" id="employee_id" value="{{ auth()->user()->employee->id }}">
    @else
    <div class="mb-4">
        <label for="employee_id" class="form-label fw-semibold text-dark">Employee</label>
        <select name="employee_id" id="employee_id" class="form-select form-select-lg shadow-sm" required>
            <option value="">-- Select Employee --</option>
            @foreach($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
            @endforeach
        </select>
        @error('employee_id')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <!-- Hidden Date -->
    <input type="hidden" name="date" id="date" value="{{ now('Africa/Nairobi')->format('Y-m-d') }}">

    <div class="form-check mb-4">
        <input type="checkbox" name="is_absent" id="is_absent" value="1" class="form-check-input">
        <label for="is_absent" class="form-check-label">Mark as Absent</label>
    </div>

    <div class="mb-4">
        <label for="remarks" class="form-label fw-semibold text-dark">Remarks</label>
        <textarea name="remarks" id="remarks" class="form-control shadow-sm" rows="4"
            placeholder="Optional remarks"></textarea>
    </div>

    <button type="button" onclick="clockIn(this)"
        class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-check-circle"></i> Clock In
    </button>
</form>