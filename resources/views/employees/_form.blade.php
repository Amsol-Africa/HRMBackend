<form id="employeeForm" enctype="multipart/form-data" class="needs-validation p-3 rounded" novalidate>
    @csrf
    @if(isset($employee))
    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
    @endif

    <!-- Tabs -->
    <ul class="nav nav-pills mb-3 border-bottom" id="employeeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active px-3 py-2" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                type="button" role="tab">
                <i class="fa fa-user me-1"></i> Personal
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link px-3 py-2" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment"
                type="button" role="tab">
                <i class="fa fa-credit-card me-1"></i> Payment
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Personal Tab (unchanged) -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel">
            <!-- Personal Info Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Personal Info</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="first_name" id="first_name" class="form-control border-primary"
                            value="{{ isset($employee) ? explode(' ', $employee->user->name)[0] : '' }}"
                            placeholder="First Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="last_name" id="last_name" class="form-control border-primary"
                            value="{{ isset($employee) ? (explode(' ', $employee->user->name)[1] ?? '') : '' }}"
                            placeholder="Last Name" required>
                    </div>
                    <div class="col-md-6">
                        <select name="gender" id="gender" class="form-select border-primary" required>
                            <option value="">Select Gender</option>
                            <option value="Male"
                                {{ isset($employee) && $employee->gender === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female"
                                {{ isset($employee) && $employee->gender === 'Female' ? 'selected' : '' }}>Female
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->date_of_birth : '' }}" required>
                    </div>
                    <div class="col-md-6">
                        <select name="marital_status" id="marital_status" class="form-select border-primary" required>
                            <option value="">Select Marital Status</option>
                            <option value="Single"
                                {{ isset($employee) && $employee->marital_status === 'Single' ? 'selected' : '' }}>
                                Single</option>
                            <option value="Married"
                                {{ isset($employee) && $employee->marital_status === 'Married' ? 'selected' : '' }}>
                                Married</option>
                            <option value="Divorced"
                                {{ isset($employee) && $employee->marital_status === 'Divorced' ? 'selected' : '' }}>
                                Divorced</option>
                            <option value="Widowed"
                                {{ isset($employee) && $employee->marital_status === 'Widowed' ? 'selected' : '' }}>
                                Widowed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Contact</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="email" name="email" id="email" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->user->email : '' }}" placeholder="Email" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="phone" id="phone" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->phone : '' }}" placeholder="Phone" required>
                    </div>
                    <div class="col-12">
                        <input type="text" name="permanent_address" id="permanent_address"
                            class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->permanent_address : '' }}"
                            placeholder="Permanent Address" required>
                    </div>
                </div>
            </div>

            <!-- Identification Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Identification</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="national_id" id="national_id" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->national_id : '' }}" placeholder="National ID"
                            required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="tax_no" id="tax_no" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->tax_no : '' }}" placeholder="Tax Number" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="nhif_no" id="nhif_no" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->nhif_no : '' }}" placeholder="NHIF Number" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="nssf_no" id="nssf_no" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->nssf_no : '' }}" placeholder="NSSF Number" required>
                    </div>
                </div>
            </div>

            <!-- Work Info Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Work Info</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="employee_code" id="employee_code" class="form-control border-primary"
                            value="{{ isset($employee) ? $employee->employee_code : '' }}" placeholder="Employee Code"
                            required>
                    </div>
                    <div class="col-md-4">
                        <select name="department_id" id="department_id" class="form-select border-primary">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ isset($employee) && $employee->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="location_id" id="location_id" class="form-select border-primary">
                            <option value="">{{ $business->company_name }} (Main Business)</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ isset($employee) && $employee->location_id == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Profile Picture -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Profile Picture</h6>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control border-primary"
                    accept="image/*" onchange="previewImage(event)">
                <img id="profile_preview"
                    src="{{ isset($employee) && $employee->getFirstMediaUrl('avatars') ? $employee->getFirstMediaUrl('avatars') : '' }}"
                    class="mt-2 rounded"
                    style="max-width: 100px; display: {{ isset($employee) && $employee->getFirstMediaUrl('avatars') ? 'block' : 'none' }};">
            </div>
        </div>

        <!-- Payment Tab -->
        <div class="tab-pane fade" id="payment" role="tabpanel">
            <!-- Salary Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Salary</h6>
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="number" name="basic_salary" id="basic_salary" class="form-control border-primary"
                            value="{{ isset($employee) ? (optional($employee->paymentDetails)->basic_salary ?? '') : '' }}"
                            placeholder="Basic Salary">
                    </div>
                    <div class="col-md-4">
                        <select name="currency" id="currency" class="form-select border-primary" required>
                            <option value="">Select Currency</option>
                            <option value="KES"
                                {{ isset($employee) && optional($employee->paymentDetails)->currency === 'KES' ? 'selected' : '' }}>
                                KES</option>
                            <option value="USD"
                                {{ isset($employee) && optional($employee->paymentDetails)->currency === 'USD' ? 'selected' : '' }}>
                                USD</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bank Details Group -->
            <div class="mb-3">
                <h6 class="text-muted fw-semibold mb-2">Bank Details</h6>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="account_name" id="account_name" class="form-control border-primary"
                            value="{{ isset($employee) ? (optional($employee->paymentDetails)->account_name ?? '') : '' }}"
                            placeholder="Account Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="account_number" id="account_number" class="form-control border-primary"
                            value="{{ isset($employee) ? (optional($employee->paymentDetails)->account_number ?? '') : '' }}"
                            placeholder="Account Number" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="bank_name" id="bank_name" class="form-control border-primary"
                            value="{{ isset($employee) ? (optional($employee->paymentDetails)->bank_name ?? '') : '' }}"
                            placeholder="Bank Name" required>
                    </div>
                    <div class="col-md-6">
                        <select name="payment_mode" id="payment_mode" class="form-select border-primary" required>
                            <option value="">Select Payment Mode</option>
                            <option value="Bank"
                                {{ isset($employee) && optional($employee->paymentDetails)->payment_mode === 'Bank' ? 'selected' : '' }}>
                                Bank</option>
                            <option value="M-Pesa"
                                {{ isset($employee) && optional($employee->paymentDetails)->payment_mode === 'M-Pesa' ? 'selected' : '' }}>
                                M-Pesa</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-3 text-end">
        <button type="button" class="btn btn-primary btn-modern px-4 py-2" onclick="saveEmployee(this)">
            <i class="fa fa-save me-2"></i> {{ isset($employee) ? 'Update' : 'Create' }} Employee
        </button>
    </div>
</form>

<style>
    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: border-color 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: none;
    }

    .nav-pills .nav-link {
        border-radius: 0;
        color: #6c757d;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        background-color: transparent;
        color: #007bff;
        border-bottom: 2px solid #007bff;
    }

    .btn-modern {
        border-radius: 20px;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .btn-modern:hover {
        background-color: #0056b3;
    }

    .bg-light {
        background-color: #f8f9fa;
    }
</style>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('profile_preview');
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>