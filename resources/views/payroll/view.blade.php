<x-app-layout title="{{ $page }}">
    <div class=" container py-5" id="payroll-document">
        <!-- Header Section -->
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
                    <!-- Download Column Dropdown -->
                    <div class="ml-5 mx-3">
                        <button class="btn btn-primary dropdown-toggle d-flex align-items-center" type="button"
                            id="downloadColumnDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-radius: 8px; font-weight: 600; white-space: nowrap;">
                            <i class="bi bi-download me-2"></i> Download Column
                        </button>
                        <div class="dropdown-menu modern-dropdown shadow-sm" aria-labelledby="downloadColumnDropdown"
                            style="min-width: 12rem;">
                            @foreach([
                            'basic_salary' => 'Basic Salary',
                            'gross_pay' => 'Gross Pay',
                            'overtime' => 'Overtime',
                            'shif' => 'SHIF',
                            'nssf' => 'NSSF',
                            'paye' => 'PAYE',
                            'housing_levy' => 'Housing Levy',
                            'helb' => 'HELB',
                            'loans' => 'Loans',
                            'advances' => 'Advances',
                            'net_pay' => 'Net Pay'
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

                    <!-- AI Insights Button -->
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
                            <th>Net Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->employeePayrolls as $index => $ep)
                        <?php $deductions = json_decode($ep->deductions, true) ?? []; ?>
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ep->employee->user->name ?? 'N/A' }}</td>
                            <td>{{ number_format($ep->basic_salary, 2) }}</td>
                            <td>{{ number_format($ep->gross_pay, 2) }}</td>
                            <td>{{ number_format($ep->overtime, 2) }}</td>
                            <td>{{ number_format($deductions['shif'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['nssf'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['paye'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['nhdf'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['helb'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['loan_repayment'] ?? 0, 2) }}</td>
                            <td>{{ number_format($deductions['advance_recovery'] ?? 0, 2) }}</td>
                            <td>{{ number_format($ep->net_pay, 2) }}</td>
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
                            <td class="fw-bold">{{ number_format($totals['totalNetPay'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer Section -->
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

        <!-- Action Buttons -->
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
                        <p class="text-muted">Here are some insights based on the current payroll data:</p>
                        <ul>
                            <li>Total Net Pay: {{ number_format($totals['totalNetPay'], 2) }}
                                {{ $payroll->currency ?? 'KES' }}
                            </li>
                            <li>Average Net Pay per Employee:
                                {{ number_format($totals['totalNetPay'] / ($payroll->employeePayrolls->count() ?: 1), 2) }}
                                {{ $payroll->currency ?? 'KES' }}
                            </li>
                            <li>Highest Deduction: PAYE ({{ number_format($totals['totalPaye'], 2) }})</li>
                        </ul>
                        <h6 class="fw-bold mt-4">Recommendations</h6>
                        <p class="text-muted">Consider the following actions to optimize payroll:</p>
                        <ul>
                            <li>Review overtime costs, which are currently
                                {{ number_format($totals['totalOvertime'], 2) }}.
                            </li>
                            <li>Assess loan and advance policies to reduce financial strain on employees.</li>
                        </ul>
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
        /* Modern, Minimalistic Styling */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa;
        }

        .container {
            max-width: 1400px;
        }

        /* Header, Body, Footer Styling */
        .invoice-header,
        .invoice-body,
        .invoice-footer {
            background-color: #fff;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .invoice-header:hover,
        .invoice-footer:hover,
        .table-responsive:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .dropdown button {
            width: 100% !important;
            height: 40px !important;
        }

        .text-dark {
            color: #1a202c !important;
        }

        .text-muted {
            color: #6b7280 !important;
        }

        /* Modern Table Styling */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table th,
        .modern-table td {
            padding: 12px 16px;
            text-align: right;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .modern-table th {
            background-color: #1a202c;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-table th:first-child,
        .modern-table td:first-child {
            text-align: left;
        }

        .modern-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .modern-table tfoot td {
            background-color: #f9fafb;
            font-weight: 600;
            color: #1a202c;
        }

        /* Modern Button and Dropdown Styling */
        .modern-btn {
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .modern-btn i {
            font-size: 1rem;
        }

        .btn-primary.modern-btn {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-primary.modern-btn:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .btn-outline-primary.modern-btn {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-outline-primary.modern-btn:hover {
            background-color: #3b82f6;
            color: #fff;
        }

        .btn-outline-secondary.modern-btn {
            border-color: #6b7280;
            color: #6b7280;
        }

        .btn-outline-secondary.modern-btn:hover {
            background-color: #6b7280;
            color: #fff;
        }

        .btn-success.modern-btn {
            background-color: #10b981;
            border-color: #10b981;
        }

        .btn-success.modern-btn:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .btn-outline-info.modern-btn {
            border-color: #0ea5e9;
            color: #0ea5e9;
        }

        .btn-outline-info.modern-btn:hover {
            background-color: #0ea5e9;
            color: #fff;
        }

        .modern-dropdown {
            border: none;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .modern-dropdown .dropdown-item {
            padding: 8px 16px;
            font-size: 0.9rem;
            color: #1a202c;
            transition: background-color 0.2s ease;
        }

        .modern-dropdown .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-footer {
            border-top: 1px solid #e5e7eb;
        }


        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .invoice-header .row {
                flex-direction: column;
                text-align: center;
            }

            .invoice-header .col-md-6 {
                margin-bottom: 1rem;
            }

            .invoice-header img,
            .invoice-header .bg-light {
                margin: 0 auto;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .d-flex.align-items-center {
                width: 100%;
                justify-content: flex-start;
            }

            .modern-btn {
                width: 100%;
                text-align: left;
            }

            .text-md-end {
                text-align: center !important;
            }

            .mt-4.text-center {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function sendPayslips(payrollId) {
            fetch('/payroll/send-payslips/' + payrollId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => Swal.fire('Success!', data.message, 'success'))
                .catch(error => Swal.fire('Error!', 'Failed to send payslips.', 'error'));
        }

        function printPayroll() {
            window.print();
        }

        document.querySelectorAll('.download-column').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const column = this.getAttribute('data-column');
                const format = this.getAttribute('data-format');
                const entitySlug = '{{ $entity->slug }}';
                const entityType = '{{ $entity->type }}';
                const payrollId = '{{ $payroll->id }}';
                const businessSlug = '{{ $business->slug }}';

                if (entityType === "location") {
                    const url =
                        '{{ route("business.payroll.download_column", [ "business" => ":businessSlug", "id" => ":payroll_id", "column" => ":column", "format" => ":format"]) }}'
                        .replace(':entitySlug', entitySlug)
                        .replace(':payroll_id', payrollId)
                        .replace(':column', column)
                        .replace(':format', format);

                    console.log("Generated URL:", url);
                    window.location.href = url;
                } else {
                    const url =
                        '{{ route("business.payroll.download_column", [ "business" => ":entitySlug", "id" => ":payroll_id", "column" => ":column", "format" => ":format"]) }}'
                        .replace(':entitySlug', entitySlug)
                        .replace(':payroll_id', payrollId)
                        .replace(':column', column)
                        .replace(':format', format);

                    console.log("Generated URL:", url);
                    window.location.href = url;
                }


            });
        });

        // Update dropdown button text on selection
        document.querySelectorAll('.dropdown-item.download-column').forEach(item => {
            item.addEventListener('click', function(e) {
                const column = this.getAttribute('data-column');
                const format = this.getAttribute('data-format');
                const button = document.getElementById('downloadColumnDropdown');
                button.innerHTML =
                    `<i class="bi bi-download me-2"></i> Download ${column} (${format.toUpperCase()})`;
            });
        });
    </script>
    @endpush
</x-app-layout>