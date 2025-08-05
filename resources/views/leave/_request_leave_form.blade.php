<form id="leaveForm" method="post" enctype="multipart/form-data">
    @csrf

    @if ((auth()->user()->hasRole('admin') || auth()->user()->hasRole('business-admin')) && (session('active_role') == 'admin' || session('active_role') == 'business-admin'))

        <div class="mb-3">
            <label for="business_location" class="form-label">Select Business or Location</label>
            <select class="form-select" id="business_location" name="business_location" required>
                <option value="" disabled selected>Select Location</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->slug }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="employee" class="form-label">Select an Employee</label>
            <select class="form-select" id="employee" name="employee_id" required>
                <option value="" disabled selected>Select Employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                @endforeach
            </select>
        </div>

    @endif

    <div class="mb-3">
        <label for="leave_type" class="form-label">Leave Type</label>
        <select class="form-select" id="leave_type" name="leave_type_id" required>
            <option value="" disabled selected>Select Leave Type</option>
            @foreach ($leaveTypes as $leaveType)
                <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" class="form-control datepicker" id="start_date" placeholder="Start Date" name="start_date" required min="{{ date('Y-m-d') }}">
    </div>

    <div class="mb-3">
        <label for="reason" class="form-label">Reason for Leave</label>
        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Provide a brief reason..."></textarea>
    </div>

    <div class="text-center">
        <button type="button" onclick="saveLeave(this)" class="btn btn-primary w-100"> <i
                class="bi bi-check-circle"></i> Submit Leave Request</button>
    </div>
</form>
