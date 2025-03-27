<x-app-layout title="{{ $page }}">
    <div class="container py-5" id="payroll-document">
        <!-- Header (unchanged) -->
        <div class="invoice-header mb-4 p-4 bg-white shadow-sm rounded">
            <div class="row align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    @if($entityType === 'business' && $entity->logo)
                    <img src="{{ asset('storage/' . $entity->logo) }}" alt="{{ $entity->company_name }} Logo"
                        class="me-3" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                    @elseif($entityType === 'location' && $business->logo)
                    <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->company_name }} Logo"
                        class="me-3" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                    @else
                    <div class="me-3 bg-light rounded d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <span
                            class="text-muted fw-bold">{{ strtoupper(substr($entity->company_name ?? $entity->name, 0, 1)) }}</span>
                    </div>
                    @endif
                    <div>
                        <h1 class="h4 fw-bold mb-1 text-dark">
                            {{ $entity->company_name ?? $entity->name ?? 'Default Company Name' }}
                        </h1>
                        <p class="text-muted small mb-1">{{ $entity->physical_address ?? 'Default Address' }}</p>
                        <p class="text-muted small mb-1">Phone:
                            {{ ($entityType === 'business' ? $entity->phone : $business->phone) ?? '+123-456-7890' }}
                        </p>
                        <p class="text-muted small mb-0">Email:
                            {{ ($entityType === 'business' && $entity->user ? $entity->user->email : $business->user->email) ?? 'info@company.com' }}
                        </p>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h2 class="h5 fw-bold text-dark">Payroll Statement</h2>
                    <p class="text-muted small mb-1">Payroll Period:
                        {{ $payroll->payrun_month }}/{{ $payroll->payrun_year }}
                    </p>
                    <p class="text-muted small mb-1">Payroll ID: {{ $payroll->id }}</p>
                    <p class="text-muted small mb-0">Date: {{ now()->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Payroll Details Section -->
        <div class="invoice-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="h5 fw-bold mb-0 text-dark">Payroll Details</h4>
                <div class="d-flex">
                    <div class="ml-5 mx-3">
                        <button class="btn btn-primary dropdown-toggle d-flex align-items-center" type="button"
                            id="downloadColumnDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-radius: 8px; font-weight: 600; white-space: nowrap;">
                            <i class="bi bi-download me-2"></i> Download Column
                        </button>
                        <div class="dropdown-menu modern-dropdown shadow-sm" aria-labelledby="downloadColumnDropdown"
                            style="min-width: 12rem;">
                            @foreach([
                            'basic_salary' => 'Basic Salary', 'gross_pay' => 'Gross Pay', 'net_pay' => 'Net Pay',
                            'overtime' => 'Overtime', 'shif' => 'SHIF', 'nssf' => 'NSSF', 'paye' => 'PAYE',
                            'paye_before_reliefs' => 'PAYE Before Reliefs', 'housing_levy' => 'Housing Levy',
                            'helb' => 'HELB', 'taxable_income' => 'Taxable Income', 'personal_relief' => 'Personal
                            Relief',
                            'insurance_relief' => 'Insurance Relief', 'pay_after_tax' => 'Pay After Tax',
                            'loan_repayment' => 'Loans', 'advance_recovery' => 'Advances',
                            'deductions_after_tax' => 'Deductions After Tax', 'attendance_present' => 'Days Present',
                            'attendance_absent' => 'Days Absent', 'days_in_month' => 'Days in Month',
                            'bank_name' => 'Bank Name', 'account_number' => 'Account Number'
                            ] as $columnKey => $columnName)
                            <div class="dropend">
                                <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                    style="white-space: nowrap;">{{ $columnName }}</a>
                                <div class="dropdown-menu modern-dropdown shadow-sm" style="min-width: 10rem;">
                                    <a class="dropdown-item download-column" href="#" data-column="{{ $columnKey }}"
                                        data-format="pdf" style="white-space: nowrap;">PDF</a>
                                    <a class="dropdown-item download-column" href="#" data-column="{{ $columnKey }}"
                                        data-format="csv" style="white-space: nowrap;">CSV</a>
                                    <a class="dropdown-item download-column" href="#" data-column="{{ $columnKey }}"
                                        data-format="xlsx" style="white-space: nowrap;">XLSX</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <button class="btn btn-outline-info modern-btn" data-bs-toggle="modal"
                        data-bs-target="#aiInsightsModal">
                        <i class="bi bi-lightbulb me-1"></i> AI Insights
                    </button>
                </div>
            </div>
            <div class="table-responsive shadow-sm rounded bg-white">
                <table class="table modern-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Basic Salary ({{ $payroll->currency ?? 'KES' }})</th>
                            <th>Gross Pay</th>
                            <th>Overtime</th>
                            <th>SHIF</th>
                            <th>NSSF</th>
                            <th>PAYE</th>
                            <th>Housing Levy</th>
                            <th>HELB</th>
                            <th>Loans</th>
                            <th>Advances</th>
                            <th>Custom Deductions</th>
                            <th>Taxable Income</th>
                            <th>Personal Relief</th>
                            <th>Insurance Relief</th>
                            <th>Pay After Tax</th>
                            <th>Deductions After Tax</th>
                            <th>Net Pay</th>
                            <th>Days Present</th>
                            <th>Days Absent</th>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->employeePayrolls as $index => $ep)
                        <?php
                        $deductions = json_decode($ep->deductions, true) ?? [];
                        $overtime = json_decode($ep->overtime, true) ?? ['amount' => 0];
                        $allowances = json_decode($ep->allowances, true) ?? [];
                        $customDeductions = array_filter($deductions, fn($d) => !in_array($d['name'] ?? '', ['SHIF', 'NSSF', 'PAYE', 'Housing Levy', 'HELB', 'Loan Repayment', 'Advance Recovery', 'Absenteeism Charge']));
                        $totalCustomDeductions = array_sum(array_map(fn($d) => $d['amount'] ?? 0, $customDeductions));
                        ?>
                        <tr data-employee-payroll-id="{{ $ep->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ep->employee->user->name ?? 'N/A' }}</td>
                            <td>{{ number_format($ep->basic_salary ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->gross_pay ?? 0, 2) }}</td>
                            <td>{{ number_format($overtime['amount'] ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->shif ?? ($deductions['shif'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->nssf ?? ($deductions['nssf'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->paye ?? ($deductions['paye'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->housing_levy ?? ($deductions['housing_levy'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->helb ?? ($deductions['helb'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0), 2) }}</td>
                            <td>{{ number_format($ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0), 2) }}
                            </td>
                            <td>{{ number_format($totalCustomDeductions, 2) }}</td>
                            <td>{{ number_format($ep->taxable_income ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->personal_relief ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->insurance_relief ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->pay_after_tax ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->deductions_after_tax ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->net_pay ?? 0, 2) }}</td>
                            <td>{{ $ep->attendance_present ?? 0 }}</td>
                            <td>{{ $ep->attendance_absent ?? 0 }}</td>
                            <td>{{ $ep->bank_name ?? 'N/A' }}</td>
                            <td>{{ $ep->account_number ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-dark view-payslip"
                                    data-employee-id="{{ $ep->employee_id }}" data-payroll-id="{{ $payroll->id }}"
                                    data-employee-payroll-id="{{ $ep->id }}" title="View Payslip" data-bs-toggle="modal"
                                    data-bs-target="#payslipModal">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary email-payslip me-1"
                                    data-employee-payroll-id="{{ $ep->id }}" title="Email Payslip">
                                    <i class="fa fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Totals:</td>
                            <td class="fw-bold">{{ number_format($totals['totalBasicSalary'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalGrossPay'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalOvertime'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalShif'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalNssf'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalPaye'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalHousingLevy'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalHelb'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalLoans'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalAdvances'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalCustomDeductions'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalTaxableIncome'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalPersonalRelief'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalInsuranceRelief'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalPayAfterTax'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalDeductionsAfterTax'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($totals['totalNetPay'], 2) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer and Action Buttons -->
        <div class="invoice-footer mt-5 p-4 bg-white shadow-sm rounded">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted small mb-5"><strong>Authorized By:</strong> ___________________________</p>
                    <p class="text-muted small mb-0"><strong>Date:</strong> ___________________________</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-1">Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
                    <p class="text-muted small mb-0">For official use only.</p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary modern-btn me-2" onclick="sendPayslips({{ $payroll->id }})">
                <i class="bi bi-envelope me-1"></i> Send Payslips
            </button>
            <a href="{{ route('business.payroll.reports', ['business' => $business->slug, $entityType => $entity->slug, 'id' => $payroll->id, 'format' => 'pdf']) }}"
                class="btn btn-outline-secondary modern-btn me-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
            </a>
            <a href="{{ route('business.payroll.reports', ['business' => $business->slug, $entityType => $entity->slug, 'id' => $payroll->id, 'format' => 'csv']) }}"
                class="btn btn-outline-secondary modern-btn me-2">
                <i class="bi bi-file-earmark-text me-1"></i> Download CSV
            </a>
            <a href="{{ route('business.payroll.reports', ['business' => $business->slug, $entityType => $entity->slug, 'id' => $payroll->id, 'format' => 'xlsx']) }}"
                class="btn btn-outline-secondary modern-btn me-2">
                <i class="bi bi-file-earmark-excel me-1"></i> Download XLSX
            </a>
        </div>

        <!-- AI Insights Modal -->
        <div class="modal fade" id="aiInsightsModal" tabindex="-1" aria-labelledby="aiInsightsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aiInsightsModalLabel">AI Insights</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="fw-bold">Payroll Analysis</h6>
                        <p class="text-muted">Insights based on current payroll data:</p>
                        <ul>
                            <li>Total Net Pay: {{ number_format($totals['totalNetPay'], 2) }}
                                {{ $payroll->currency ?? 'KES' }}
                            </li>
                            <li>Average Net Pay:
                                {{ number_format($totals['totalNetPay'] / ($payroll->employeePayrolls->count() ?: 1), 2) }}
                                {{ $payroll->currency ?? 'KES' }}
                            </li>
                            <li>Total Taxable Income: {{ number_format($totals['totalTaxableIncome'], 2) }}</li>
                            <li>Total Statutory Deductions:
                                {{ number_format($totals['totalShif'] + $totals['totalNssf'] + $totals['totalPaye'] + $totals['totalHousingLevy'] + $totals['totalHelb'], 2) }}
                            </li>
                        </ul>
                        <h6 class="fw-bold mt-4">Recommendations</h6>
                        <p class="text-muted">Suggestions to optimize payroll:</p>
                        <ul>
                            <li>Overtime costs ({{ number_format($totals['totalOvertime'], 2) }}) could be reviewed for
                                efficiency.</li>
                            <li>High loan repayments ({{ number_format($totals['totalLoans'], 2) }}) suggest assessing
                                employee financial support policies.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modern-btn"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip Modal -->
        <div class="modal fade" id="payslipModal" tabindex="-1" aria-labelledby="payslipModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 550px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="payslipModalBody">
                        <p>Loading payslip...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary modern-btn"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    .email-payslip,
    .view-payslip {
        padding: 4px 8px;
        font-size: 0.85rem;
    }

    .email-payslip i,
    .view-payslip i {
        font-size: 1rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .modern-table th,
    .modern-table td {
        white-space: nowrap;
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    function sendPayslips(payrollId) {
        const businessSlug = '{{ $business->slug }}';
        fetch(`/business/${businessSlug}/payroll/send-payslips`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    payroll_id: payrollId
                })
            })
            .then(response => response.ok ? response.json() : response.text().then(text => {
                throw new Error(text);
            }))
            .then(data => Swal.fire('Success!', data.message || 'Payslips queued for sending.', 'success'))
            .catch(error => Swal.fire('Error!', error.message || 'Failed to send payslips.', 'error'));
    }

    document.querySelectorAll('.email-payslip').forEach(button => {
        button.addEventListener('click', function() {
            const employeePayrollId = this.getAttribute('data-employee-payroll-id');
            const businessSlug = '{{ $business->slug }}';
            fetch(`/business/${businessSlug}/payroll/send-payslips`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        employee_payroll_id: employeePayrollId
                    })
                })
                .then(response => response.ok ? response.json() : response.text().then(text => {
                    throw new Error(text);
                }))
                .then(data => Swal.fire('Success!', data.message || 'Payslip queued for sending.',
                    'success'))
                .catch(error => Swal.fire('Error!', error.message || 'Failed to send payslip.',
                    'error'));
        });
    });

    document.querySelectorAll('.view-payslip').forEach(button => {
        button.addEventListener('click', function() {
            const employeeId = this.getAttribute('data-employee-id');
            const payrollId = this.getAttribute('data-payroll-id');
            const modalBody = document.getElementById('payslipModalBody');
            const businessSlug = '{{ $business->slug }}';
            modalBody.innerHTML = '<p>Loading payslip...</p>';
            fetch(`/business/${businessSlug}/payroll/payslip/${employeeId}?payroll_id=${payrollId}`)
                .then(response => response.ok ? response.text() : Promise.reject(
                    'Failed to load payslip'))
                .then(html => modalBody.innerHTML = html)
                .catch(error => modalBody.innerHTML =
                    '<p>Error loading payslip. Please try again.</p>');
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".download-column").forEach(item => {
            item.addEventListener("click", function(e) {
                e.preventDefault();
                const column = this.getAttribute("data-column");
                const format = this.getAttribute("data-format");
                const button = document.getElementById("downloadColumnDropdown");
                const businessSlug = '{{ $business->slug }}';
                const payrollId = '{{ $payroll->id }}';
                button.innerHTML =
                    `<i class="bi bi-download me-2"></i> Downloading ${column} (${format.toUpperCase()})`;
                const url =
                    `{{ route('business.payroll.download_column', ['business' => $business->slug, 'id' => $payroll->id, 'column' => ':column', 'format' => ':format']) }}`
                    .replace(':column', column)
                    .replace(':format', format);
                console.log("Generated URL:", url);
                window.location.href = url;
                setTimeout(() => {
                    button.innerHTML =
                        `<i class="bi bi-download me-2"></i> Download Column`;
                }, 2000);
            });
        });
    });
    </script>
    @endpush
</x-app-layout>