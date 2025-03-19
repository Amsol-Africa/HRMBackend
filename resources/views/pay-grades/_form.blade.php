<form id="payGradeForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($payGrade))
    <input type="hidden" name="pay_grade_id" value="{{ $payGrade->id }}">
    @endif
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label fw-medium text-dark">Pay Grade Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $payGrade->name ?? '' }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="amount" class="form-label fw-medium text-dark">Amount (Monthly)</label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ $payGrade->amount ?? '' }}"
                step="0.01" required>
            @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="job_category_id" class="form-label fw-medium text-dark">Job Category (Optional)</label>
            <select name="job_category_id" id="job_category_id" class="form-select">
                <option value="" {{ !isset($payGrade) || !$payGrade->job_category_id ? 'selected' : '' }}>None</option>
                @foreach ($jobCategories as $category)
                <option value="{{ $category->id }}"
                    {{ isset($payGrade) && $payGrade->job_category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            @error('job_category_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="department_id" class="form-label fw-medium text-dark">Department (Optional)</label>
            <select name="department_id" id="department_id" class="form-select">
                <option value="" {{ !isset($payGrade) || !$payGrade->department_id ? 'selected' : '' }}>None</option>
                @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    {{ isset($payGrade) && $payGrade->department_id == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
                @endforeach
            </select>
            @error('department_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="mt-4">
        <button type="button" class="btn btn-primary btn-modern" onclick="savePayGrade(this)">
            <i class="fa fa-save me-2"></i> {{ isset($payGrade) ? 'Update Pay Grade' : 'Create Pay Grade' }}
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
            event.preventDefault();
            event.stopPropagation();
            if (form.checkValidity()) {
                savePayGrade(form.querySelector('button[type="button"]'));
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
@endpush