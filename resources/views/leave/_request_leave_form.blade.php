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
                <option value="{{ $leaveType->id }}" data-requires-attachment="{{ $leaveType->requires_attachment ? '1' : '0' }}">
                    {{ $leaveType->name }}
                </option>
            @endforeach
        </select>
        <small id="remainingDays" class="text-muted"></small>
    </div>

    {{-- Attachment field (hidden by default) --}}
    <div class="mb-3 d-none" id="attachmentField">
        <label for="attachment" class="form-label">Attachment (Required for this leave type)</label>
        <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.jpg,.png,.doc,.docx">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control datepicker" id="start_date" name="start_date" required min="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-6 mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control datepicker" id="end_date" name="end_date" required min="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div class="mb-3">
        <label for="reason" class="form-label">Reason for Leave</label>
        <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Provide a brief reason..."></textarea>
    </div>

    <div class="text-center">
        <button type="button" onclick="saveLeave(this)" class="btn btn-primary w-100"> 
            <i class="bi bi-check-circle"></i> Submit Leave Request
        </button>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let leaveTypeSelect = document.getElementById('leave_type');
    let employeeSelect = document.getElementById('employee');

    leaveTypeSelect.addEventListener('change', function() {
        let leaveTypeId = this.value;
        let employeeId = employeeSelect ? employeeSelect.value : null;

        if (leaveTypeId) {
            fetch("{{ route('leave-types.remaining-days') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    leave_type_id: leaveTypeId,
                    employee_id: employeeId
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('remainingDays').innerText = 
                    "Remaining Days: " + data.remaining_days;
            });
        }
    });
});
</script>
