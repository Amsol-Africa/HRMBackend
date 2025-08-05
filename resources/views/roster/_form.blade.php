<form id="rostersForm" method="post">
    @csrf
    @if (isset($roster))
    <input type="hidden" name="roster_slug" id="roster_slug" value="{{ $roster->slug }}">
    @endif

    <div class="mb-3">
        <label for="roster_name" class="form-label">Roster Name</label>
        <input type="text" class="form-control" id="roster_name" name="name" required
            placeholder="e.g. May-June 2025 Roster" value="{{ isset($roster) ? $roster->name : old('name') }}">
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control date-picker" id="start_date" name="start_date" required
                value="{{ isset($roster) ? $roster->start_date->format('Y-m-d') : old('start_date') }}">
        </div>
        <div class="col-md-6">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control date-picker" id="end_date" name="end_date" required
                value="{{ isset($roster) ? $roster->end_date->format('Y-m-d') : old('end_date') }}">
        </div>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status" required>
            <option value="draft" {{ isset($roster) && $roster->status === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ isset($roster) && $roster->status === 'published' ? 'selected' : '' }}>
                Published</option>
            <option value="closed" {{ isset($roster) && $roster->status === 'closed' ? 'selected' : '' }}>Closed
            </option>
        </select>
    </div>

    <h6 class="mt-4 mb-3">Assignments</h6>
    <div id="assignmentsContainer">
        @if (isset($roster) && $roster->assignments->count())
        @foreach ($roster->assignments as $index => $assignment)
        <div class="assignment-row mb-4 p-3 border rounded">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][employee_id]" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}"
                            {{ $assignment->employee_id == $employee->id ? 'selected' : '' }}>
                            {{ $employee->user->first_name }} {{ $employee->user->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][department_id]" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            {{ $assignment->department_id == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][job_category_id]" required>
                        <option value="">Select Job Category</option>
                        @foreach ($jobCategories as $jobCategory)
                        <option value="{{ $jobCategory->id }}"
                            {{ $assignment->job_category_id == $jobCategory->id ? 'selected' : '' }}>
                            {{ $jobCategory->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][location_id]" required>
                        <option value="">Select Location</option>
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}"
                            {{ $assignment->location_id == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-3">
                    <input type="date" class="form-control date-picker" name="assignments[{{ $index }}][date]" required
                        value="{{ $assignment->date->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][shift_id]">
                        <option value="">No Shift</option>
                        @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ $assignment->shift_id == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][leave_id]">
                        <option value="">No Leave</option>
                        @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}"
                            {{ $assignment->leave_id == $leaveType->id ? 'selected' : '' }}>
                            {{ $leaveType->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="assignments[{{ $index }}][overtime_hours]"
                        step="0.01" min="0" placeholder="Overtime Hours" value="{{ $assignment->overtime_hours }}">
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-6">
                    <textarea class="form-control" name="assignments[{{ $index }}][notes]"
                        placeholder="Notes">{{ $assignment->notes }}</textarea>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[{{ $index }}][notification_type]" required>
                        <option value="none" {{ $assignment->notification_type == 'none' ? 'selected' : '' }}>No
                            Notification</option>
                        <option value="email" {{ $assignment->notification_type == 'email' ? 'selected' : '' }}>Email
                        </option>
                        <option value="in_app" {{ $assignment->notification_type == 'in_app' ? 'selected' : '' }}>In-App
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-sm remove-assignment">Remove</button>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div class="assignment-row mb-4 p-3 border rounded">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][employee_id]" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][department_id]" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][job_category_id]" required>
                        <option value="">Select Job Category</option>
                        @foreach ($jobCategories as $jobCategory)
                        <option value="{{ $jobCategory->id }}">{{ $jobCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][location_id]" required>
                        <option value="">Select Location</option>
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-3">
                    <input type="date" class="form-control date-picker" name="assignments[0][date]" required>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][shift_id]">
                        <option value="">No Shift</option>
                        @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][leave_id]">
                        <option value="">No Leave</option>
                        @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="assignments[0][overtime_hours]" step="0.01" min="0"
                        placeholder="Overtime Hours">
                </div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-6">
                    <textarea class="form-control" name="assignments[0][notes]" placeholder="Notes"></textarea>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="assignments[0][notification_type]" required>
                        <option value="none">No Notification</option>
                        <option value="email">Email</option>
                        <option value="in_app">In-App</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-sm remove-assignment">Remove</button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-outline-secondary" id="addAssignment">
            <i class="fas fa-plus"></i> Add Assignment
        </button>
        <button type="button" class="btn btn-primary" id="submitButton">
            <i class="bi bi-check-circle"></i> {{ isset($roster) ? 'Update Roster' : 'Save Roster' }}
        </button>
    </div>
</form>

@push('styles')
<style>
.assignment-row {
    transition: background-color 0.3s;
}

.assignment-row:hover {
    background-color: #f8f9fa;
}

.form-select,
.form-control {
    border-radius: 0.375rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    $(".date-picker").flatpickr({
        dateFormat: "Y-m-d",
    });

    let assignmentIndex = {
        {
            isset($roster) ? $roster - > assignments - > count() : 1
        }
    };

    $('#addAssignment').click(function() {
        const newRow = $('.assignment-row:first').clone();
        newRow.find('select, input, textarea').each(function() {
            const name = $(this).attr('name').replace(/\[\d+\]/, `[${assignmentIndex}]`);
            $(this).attr('name', name).val('');
        });
        newRow.find('.date-picker').flatpickr({
            dateFormat: "Y-m-d"
        });
        $('#assignmentsContainer').append(newRow);
        assignmentIndex++;
    });

    $(document).on('click', '.remove-assignment', function() {
        if ($('.assignment-row').length > 1) {
            $(this).closest('.assignment-row').remove();
        } else {
            Swal.fire('Warning', 'At least one assignment is required.', 'warning');
        }
    });
});
</script>
@endpush