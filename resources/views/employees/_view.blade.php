<div class="container mt-5">
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal"
                        role="tab">Personal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment" role="tab">Payment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="employment-tab" data-bs-toggle="tab" href="#employment"
                        role="tab">Employment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="additional-tab" data-bs-toggle="tab" href="#additional"
                        role="tab">Additional</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-bs-toggle="tab" href="#documents"
                        role="tab">Documents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="actions-tab" data-bs-toggle="tab" href="#actions" role="tab">Actions</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Personal Details Tab -->
                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <img src="{{ $employee->getFirstMediaUrl('avatars') ?: 'https://via.placeholder.com/80' }}"
                                class="rounded-circle border object-fit-cover" style="width: 80px; height: 80px;"
                                alt="Profile">
                        </div>
                        <div class="col">
                            <h5 class="fw-semibold mb-1">{{ $employee->user->name }}</h5>
                            <p class="text-muted mb-0">{{ $employee->user->email ?? 'No Email' }}</p>
                            <span class="badge bg-success-subtle text-success fw-normal mt-1">
                                {{ $employee->jobCategory?->name ?? 'Not Assigned' }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Basic Info -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Basic Information</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Employee Code</dt>
                                <dd class="col-7">{{ $employee->employee_code ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Gender</dt>
                                <dd class="col-7">{{ ucfirst($employee->gender) ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Phone</dt>
                                <dd class="col-7">
                                    {{ !empty($employee->user->phone) ? $employee->user->phone : ($employee->alternate_phone ?? 'N/A') }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Marital Status</dt>
                                <dd class="col-7">{{ ucfirst($employee->marital_status) ?? 'N/A' }}</dd>
                            </dl>
                        </div>

                        <!-- Identification -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Identification</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">National ID</dt>
                                <dd class="col-7">{{ $employee->national_id ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Tax No</dt>
                                <dd class="col-7">{{ $employee->tax_no ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">NHIF No</dt>
                                <dd class="col-7">{{ $employee->nhif_no ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">NSSF No</dt>
                                <dd class="col-7">{{ $employee->nssf_no ?? 'N/A' }}</dd>
                            </dl>
                        </div>

                        <!-- Passport & Birth -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Passport & Birth</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Passport No</dt>
                                <dd class="col-7">{{ $employee->passport_no ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Issue Date</dt>
                                <dd class="col-7">
                                    {{ $employee->passport_issue_date ? date('d M Y', strtotime($employee->passport_issue_date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Expiry Date</dt>
                                <dd class="col-7">
                                    {{ $employee->passport_expiry_date ? date('d M Y', strtotime($employee->passport_expiry_date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Date of Birth</dt>
                                <dd class="col-7">
                                    {{ $employee->date_of_birth ? date('d M Y', strtotime($employee->date_of_birth)) : 'N/A' }}
                                </dd>
                            </dl>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Address</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Current Address</dt>
                                <dd class="col-7">{{ $employee->address ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Permanent Address</dt>
                                <dd class="col-7">{{ $employee->permanent_address ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Blood Group</dt>
                                <dd class="col-7">{{ $employee->blood_group ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Tab -->
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <div class="row g-4">
                        <!-- Salary Details -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Salary Details</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Basic Salary</dt>
                                <dd class="col-7">
                                    {{ number_format((float) ($employee->paymentDetails->basic_salary ?? 0), 2) }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Payment Mode</dt>
                                <dd class="col-7">
                                    {{ strtoupper($employee->paymentDetails->payment_mode ?? 'N/A') }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Exempt from Payroll</dt>
                                <dd class="col-7">{{ $employee->is_exempt_from_payroll ? 'Yes' : 'No' }}</dd>
                            </dl>
                        </div>

                        <!-- Bank Details -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Bank Details</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Account Name</dt>
                                <dd class="col-7">
                                    {{ $employee->paymentDetails->account_name ?? 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Account Number</dt>
                                <dd class="col-7">
                                    {{ $employee->paymentDetails->account_number ?? 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Bank Name</dt>
                                <dd class="col-7">
                                    {{ $employee->paymentDetails->bank_name ?? 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Branch</dt>
                                <dd class="col-7">
                                    {{ $employee->paymentDetails->bank_branch ?? 'N/A' }}
                                </dd>
                            </dl>
                        </div>

                        <!-- Recent Payroll Snapshot -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Recent Payroll</h6>
                            @if($employee->payrolls->isNotEmpty())
                            @php $latestPayroll = $employee->payrolls->sortByDesc('created_at')->first();
                            @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Gross Pay</dt>
                                <dd class="col-7">{{ number_format((float) ($latestPayroll->gross_pay ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Net Pay</dt>
                                <dd class="col-7">{{ number_format((float) ($latestPayroll->net_pay ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Deductions</dt>
                                <dd class="col-7">
                                    {{ number_format((float) ($latestPayroll->deductions_after_tax ?? 0), 2) }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Date</dt>
                                <dd class="col-7">
                                    {{ $latestPayroll->created_at ? date('d M Y', strtotime($latestPayroll->created_at)) : 'N/A' }}
                                </dd>
                            </dl>
                            @else
                            <p class="text-muted">No payroll records available.</p>
                            @endif
                        </div>

                        <!-- Advances -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Advances</h6>
                            @if($employee->advances->isNotEmpty())
                            @php $latestAdvance = $employee->advances->sortByDesc('created_at')->first(); @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Amount</dt>
                                <dd class="col-7">{{ number_format((float) ($latestAdvance->amount ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Date</dt>
                                <dd class="col-7">
                                    {{ $latestAdvance->date ? date('d M Y', strtotime($latestAdvance->date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Note</dt>
                                <dd class="col-7">{{ $latestAdvance->note ?? 'N/A' }}</dd>
                            </dl>
                            @else
                            <p class="text-muted">No advances recorded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Employment Details Tab -->
                <div class="tab-pane fade" id="employment" role="tabpanel">
                    <div class="row g-4">
                        <!-- Employment Info -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Employment Information</h6>
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Employee Since</dt>
                                <dd class="col-7">
                                    {{ $employee->created_at ? date('d M Y', strtotime($employee->created_at)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Department</dt>
                                <dd class="col-7">{{ $employee->department?->name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Business</dt>
                                <dd class="col-7">{{ $employee->business?->company_name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Location</dt>
                                <dd class="col-7">{{ $employee->location?->name ?? 'N/A' }}</dd>
                            </dl>
                        </div>

                        <!-- Attendance Snapshot -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Recent Attendance</h6>
                            @if($employee->attendances->isNotEmpty())
                            @php $latestAttendance = $employee->attendances->sortByDesc('date')->first(); @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Date</dt>
                                <dd class="col-7">
                                    {{ $latestAttendance->date ? date('d M Y', strtotime($latestAttendance->date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Clock In</dt>
                                <dd class="col-7">{{ $latestAttendance->clock_in ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Clock Out</dt>
                                <dd class="col-7">{{ $latestAttendance->clock_out ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Status</dt>
                                <dd class="col-7">{{ $latestAttendance->is_absent ? 'Absent' : 'Present' }}</dd>
                            </dl>
                            @else
                            <p class="text-muted">No attendance records available.</p>
                            @endif
                        </div>

                        <!-- Leave Snapshot -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Recent Leave</h6>
                            @if($employee->leaveRequests->isNotEmpty())
                            @php $latestLeave = $employee->leaveRequests->sortByDesc('created_at')->first(); @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Reference</dt>
                                <dd class="col-7">{{ $latestLeave->reference_number ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Start Date</dt>
                                <dd class="col-7">
                                    {{ $latestLeave->start_date ? date('d M Y', strtotime($latestLeave->start_date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">End Date</dt>
                                <dd class="col-7">
                                    {{ $latestLeave->end_date ? date('d M Y', strtotime($latestLeave->end_date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Total Days</dt>
                                <dd class="col-7">{{ $latestLeave->total_days ?? 'N/A' }}</dd>
                            </dl>
                            @else
                            <p class="text-muted">No leave requests available.</p>
                            @endif
                        </div>

                        <!-- Overtime Snapshot -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Recent Overtime</h6>
                            @if($employee->overtimes->isNotEmpty())
                            @php $latestOvertime = $employee->overtimes->sortByDesc('created_at')->first(); @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Date</dt>
                                <dd class="col-7">
                                    {{ $latestOvertime->date ? date('d M Y', strtotime($latestOvertime->date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">Hours</dt>
                                <dd class="col-7">{{ number_format((float) ($latestOvertime->hours ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Rate</dt>
                                <dd class="col-7">{{ number_format((float) ($latestOvertime->rate ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Status</dt>
                                <dd class="col-7"></dd>
                            </dl>
                            @else
                            <p class="text-muted">No overtime records available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Details Tab -->
                <div class="tab-pane fade" id="additional" role="tabpanel">
                    <div class="row g-4">
                        <!-- Academic Qualifications -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Academic Qualifications</h6>
                            @if($employee->academicDetails->isNotEmpty())
                            @php $latestQualification =
                            $employee->academicDetails->sortByDesc('end_date')->first(); @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Institution</dt>
                                <dd class="col-7">{{ $latestQualification->institution_name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Certification</dt>
                                <dd class="col-7">{{ $latestQualification->certification_obtained ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Start Date</dt>
                                <dd class="col-7">
                                    {{ $latestQualification->start_date ? date('d M Y', strtotime($latestQualification->start_date)) : 'N/A' }}
                                </dd>
                                <dt class="col-5 fw-medium text-muted">End Date</dt>
                                <dd class="col-7">
                                    {{ $latestQualification->end_date ? date('d M Y', strtotime($latestQualification->end_date)) : 'N/A' }}
                                </dd>
                            </dl>
                            @else
                            <p class="text-muted">No academic qualifications recorded.</p>
                            @endif
                        </div>

                        <!-- Allowances -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Allowances</h6>
                            @if($employee->employeeAllowances->isNotEmpty())
                            @php $latestAllowance = $employee->employeeAllowances->sortByDesc('created_at')->first();
                            @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Name</dt>
                                <dd class="col-7">{{ $latestAllowance->allowance?->name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Amount</dt>
                                <dd class="col-7">{{ number_format((float) ($latestAllowance->amount ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Taxable</dt>
                                <dd class="col-7">{{ $latestAllowance->allowance?->is_taxable ? 'Yes' : 'No' }}</dd>
                            </dl>
                            @else
                            <p class="text-muted">No allowances recorded.</p>
                            @endif
                        </div>

                        <!-- Deductions -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Deductions</h6>
                            @if($employee->employeeDeductions->isNotEmpty())
                            @php $latestDeduction = $employee->employeeDeductions->sortByDesc('created_at')->first();
                            @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Name</dt>
                                <dd class="col-7">{{ $latestDeduction->deduction?->name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Amount</dt>
                                <dd class="col-7">{{ number_format((float) ($latestDeduction->amount ?? 0), 2) }}</dd>
                                <dt class="col-5 fw-medium text-muted">Date</dt>
                                <dd class="col-7">
                                    {{ $latestDeduction->created_at ? date('d M Y', strtotime($latestDeduction->created_at)) : 'N/A' }}
                                </dd>
                            </dl>
                            @else
                            <p class="text-muted">No deductions recorded.</p>
                            @endif
                        </div>

                        <!-- Family Members -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Family Members</h6>
                            @if($employee->familyMembers->isNotEmpty())
                            @php $latestFamily = $employee->familyMembers->sortByDesc('created_at')->first();
                            @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Name</dt>
                                <dd class="col-7">{{ $latestFamily->name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Relationship</dt>
                                <dd class="col-7">{{ ucfirst($latestFamily->relationship ?? 'N/A') }}</dd>
                                <dt class="col-5 fw-medium text-muted">Date of Birth</dt>
                                <dd class="col-7">
                                    {{ $latestFamily->date_of_birth ? date('d M Y', strtotime($latestFamily->date_of_birth)) : 'N/A' }}
                                </dd>
                            </dl>
                            @else
                            <p class="text-muted">No family members recorded.</p>
                            @endif
                        </div>

                        <!-- Emergency Contacts -->
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-muted mb-3">Emergency Contact</h6>
                            @if($employee->emergencyContacts->isNotEmpty())
                            @php $latestContact = $employee->emergencyContacts->sortByDesc('created_at')->first();
                            @endphp
                            <dl class="row mb-0">
                                <dt class="col-5 fw-medium text-muted">Name</dt>
                                <dd class="col-7">{{ $latestContact->name ?? 'N/A' }}</dd>
                                <dt class="col-5 fw-medium text-muted">Relationship</dt>
                                <dd class="col-7">{{ ucfirst($latestContact->relationship ?? 'N/A') }}</dd>
                                <dt class="col-5 fw-medium text-muted">Phone</dt>
                                <dd class="col-7">{{ $latestContact->phone ?? 'N/A' }}</dd>
                            </dl>
                            @else
                            <p class="text-muted">No emergency contacts recorded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-muted mb-3">Documents</h6>
                            @if($employee->documents->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Document Name</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Uploaded On</th>
                                            <th scope="col">Uploaded By</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employee->documents as $document)
                                        <tr>
                                            <td>{{ $document->document_name ?? 'N/A' }}</td>
                                            <td>{{ $document->document_type ?? 'N/A' }}</td>
                                            <td>{{ $document->created_at ? date('d M Y', strtotime($document->created_at)) : 'N/A' }}
                                            </td>
                                            <td>{{ $document->uploaded_by ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ $document->file_path ? Storage::url($document->file_path) : '#' }}"
                                                    class="btn btn-sm btn-primary" target="_blank"
                                                    {{ $document->file_path ? '' : 'disabled' }}>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No documents recorded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions Tab -->
                <div class="tab-pane fade" id="actions" role="tabpanel">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-warning btn-sm flex-grow-1 flex-md-grow-0">Warn Employee</button>
                        <button class="btn btn-primary btn-sm flex-grow-1 flex-md-grow-0">Send Welcome Email</button>
                        <button class="btn btn-info btn-sm flex-grow-1 flex-md-grow-0">Request Leave</button>
                        <button class="btn btn-danger btn-sm flex-grow-1 flex-md-grow-0">Suspend</button>
                        <button class="btn btn-dark btn-sm flex-grow-1 flex-md-grow-0">Delete</button>
                        <button class="btn btn-secondary btn-sm flex-grow-1 flex-md-grow-0">Login as Employee</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
    }

    .nav-tabs .nav-link {
        color: #495057;
        padding: 0.75rem 1.5rem;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #e9ecef #e9ecef #fff;
        font-weight: 600;
    }

    .nav-tabs .nav-link:hover {
        color: #0d6efd;
    }

    .btn-sm {
        min-width: 130px;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .tab-pane {
        padding: 1rem;
    }

    h6.text-muted {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    dl dt {
        font-size: 0.9rem;
    }

    dl dd {
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 767.98px) {
        .nav-tabs .nav-link {
            padding: 0.5rem 1rem;
        }

        .btn-sm {
            width: 100%;
        }
    }
</style>