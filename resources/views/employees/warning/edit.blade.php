<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">{{ $page }}</h2>
                </div>
                <p class="text-muted mb-5">{{ $description }}</p>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Edit Warning</h4>
                        <form id="warningForm" action="{{ route('warnings.update', $warning->id) }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('POST')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="employee_id" class="form-label fw-medium text-dark">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-select" required>
                                        <option value="" disabled>Select Employee</option>
                                        @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ $warning->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="issue_date" class="form-label fw-medium text-dark">Issue Date</label>
                                    <input type="date" name="issue_date" id="issue_date" class="form-control"
                                        value="{{ $warning->issue_date->toDateString() }}" required>
                                    @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="reason" class="form-label fw-medium text-dark">Reason</label>
                                    <input type="text" name="reason" id="reason" class="form-control"
                                        value="{{ $warning->reason }}" required>
                                    @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label fw-medium text-dark">Description
                                        (Optional)</label>
                                    <textarea name="description" id="description" class="form-control"
                                        rows="3">{{ $warning->description }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="status" class="form-label fw-medium text-dark">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active" {{ $warning->status === 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="resolved"
                                            {{ $warning->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-modern">
                                    <i class="fa fa-save me-2"></i> Update Warning
                                </button>
                                <a href="{{ route('warnings.index') }}"
                                    class="btn btn-secondary btn-modern ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Bootstrap form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
    @endpush
</x-app-layout>