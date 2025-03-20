<div class="row g-4">
    <!-- Personal Details -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-3 bg-light">
            <div class="card-header bg-transparent border-bottom py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-primary me-2 fs-4"></i>
                    <h5 class="fw-bold text-primary m-0">Personal Details</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    @if($employee->getFirstMediaUrl('avatars'))
                    <img src="{{ $employee->getFirstMediaUrl('avatars') }}" class="rounded-circle border me-3"
                        style="width: 60px; height: 60px; object-fit: cover;" alt="Profile">
                    @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                        style="width: 60px; height: 60px; font-size: 24px;">
                        {{ substr($employee->user->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <h6 class="fw-semibold m-0">{{ $employee->user->name }}</h6>
                        <small class="text-muted">{{ $employee->user->email }}</small>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <p class="mb-1"><strong>Code:</strong></p>
                        <p class="text-muted">{{ $employee->employee_code }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Department:</strong></p>
                        <p class="text-muted">{{ $employee->department ? $employee->department->name : 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Location:</strong></p>
                        <p class="text-muted">{{ $employee->location ? $employee->location->name : 'Main Business' }}
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Phone:</strong></p>
                        <p class="text-muted">{{ $employee->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>National ID:</strong></p>
                        <p class="text-muted">{{ $employee->national_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Tax No:</strong></p>
                        <p class="text-muted">{{ $employee->tax_no ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-3 bg-light">
            <div class="card-header bg-transparent border-bottom py-3 px-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-credit-card text-success me-2 fs-4"></i>
                    <h5 class="fw-bold text-success m-0">Payment Details</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <p class="mb-1"><strong>Basic Salary:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->basic_salary ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Currency:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->currency ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Payment Mode:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->payment_mode ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Account Name:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->account_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Account Number:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->account_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><strong>Bank Name:</strong></p>
                        <p class="text-muted">{{ $employee->paymentDetails?->bank_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: none;
}

.card-header {
    background-color: #f8f9fa;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.text-muted {
    font-size: 0.95rem;
}

.rounded-3 {
    border-radius: 0.5rem !important;
}

.border-light {
    border-color: #dee2e6 !important;
}
</style>