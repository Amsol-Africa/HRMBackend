@php
$employees = $employees ?? collect();
@endphp
<form id="tasksForm" method="post">
    @csrf
    @if(isset($task))
    <input type="hidden" name="task_id" value="{{ $task->id }}">
    @endif

    <div class="form-group mb-3">
        <label for="task_name">Task Name</label>
        <input type="text" class="form-control" id="task_name" name="title" required placeholder="e.g Design Homepage"
            value="{{ isset($task) ? $task->title : old('title') }}">
    </div>

    <div class="form-group mb-3">
        <label for="description">Task Description</label>
        <textarea name="description" id="description" class="form-control"
            rows="4">{{ isset($task) ? $task->description : old('description', 'Short Description...') }}</textarea>
    </div>

    <div class="form-group mb-3">
        <label for="due_date">Due Date</label>
        <input type="date" class="form-control datepicker" id="due_date" name="due_date" required
            value="{{ isset($task) ? $task->due_date->format('Y-m-d') : old('due_date') }}">
    </div>

    <div class="form-group mb-3">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="pending" {{ isset($task) && $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ isset($task) && $task->status == 'in_progress' ? 'selected' : '' }}>In
                Progress
            </option>
            <option value="completed" {{ isset($task) && $task->status == 'completed' ? 'selected' : '' }}>Completed
            </option>
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="employee_ids">Assign Employees</label>
        <select class="form-control select2" id="employee_ids" name="employee_ids[]" multiple>
            @foreach($employees as $employee)
            <option value="{{ $employee->id }}"
                {{ isset($task) && $task->employees->contains($employee->id) ? 'selected' : '' }}>
                {{ $employee->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <button onclick="saveTask(this)" type="button" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> {{ isset($task) ? 'Update Task' : 'Save Task' }}
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#employee_ids').select2({
        placeholder: "Select employees",
        allowClear: true
    });
});
</script>