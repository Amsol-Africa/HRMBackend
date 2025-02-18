<form id="leaveForm" method="post" enctype="multipart/form-data">
    @csrf

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
        <input type="date" class="form-control datepicker" id="start_date" name="start_date" required
            min="{{ date('Y-m-d') }}">
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
