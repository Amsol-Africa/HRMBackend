<!-- resources/views/employees/warning/_form.blade.php -->
<form id="warningForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($warning))
    <input type="hidden" name="warning_id" value="{{ $warning->id }}">
    @endif
    <div class="row g-3">
        <div class="col-12">
            <label for="employee_id" class="form-label fw-medium text-dark">Employee</label>
            <select name="employee_id" id="employee_id" class="form-select" required>
                <option value="" disabled {{ !isset($warning) ? 'selected' : '' }}>Select Employee</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ isset($warning) && $warning->employee_id == $employee->id ? 'selected' : '' }}>
                    {{ $employee->user->name }}
                </option>
                @endforeach
            </select>
            @error('employee_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="issue_date" class="form-label fw-medium text-dark">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control"
                value="{{ isset($warning) ? $warning->issue_date->toDateString() : now()->toDateString() }}" required>
            @error('issue_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="reason" class="form-label fw-medium text-dark">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control" value="{{ $warning->reason ?? '' }}"
                required>
            @error('reason')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label fw-medium text-dark">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control"
                rows="3">{{ $warning->description ?? '' }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @if(isset($warning))
        <div class="col-12">
            <label for="status" class="form-label fw-medium text-dark">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="active" {{ $warning->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="resolved" {{ $warning->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @endif
    </div>
    <div class="mt-4">
        <button type="button" class="btn btn-primary btn-modern" onclick="saveWarning(this)">
            <i class="fa fa-save me-2"></i> {{ isset($warning) ? 'Update Warning' : 'Issue Warning' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.stopPropagation();
            if (form.checkValidity()) {
                saveWarning(form.querySelector(
                    'button[type="button"]'));
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
@endpush