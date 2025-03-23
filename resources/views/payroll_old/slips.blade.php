<x-app-layout>

    <div class="container-fluid px-0">
        <div class="row g-2 mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-calendar-week me-2 text-primary fs-4"></i> <span class="fw-bold">Period</span>
                        </h6>
                        <p id="period" class="fs-5 mt-2">February 2025</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-calendar-check me-2 text-success fs-4"></i> <span class="fw-bold">Pay
                                Day</span>
                        </h6>
                        <p id="pay-day" class="fs-5 mt-2">28th February 2025</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-people me-2 text-info fs-4"></i> <span class="fw-bold">Employees</span>
                        </h6>
                        <p id="employees" class="fs-5 mt-2">2 Employees</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-cash-stack me-2 text-warning fs-4"></i> <span class="fw-bold">Payroll
                                Cost</span>
                        </h6>
                        <p id="payroll-cost" class="fs-5 mt-2">KES 123,000</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2 text-secondary fs-4"></i> <span class="fw-bold">Net Pay</span>
                        </h6>
                        <p id="net-pay" class="fs-5 mt-2">KES 96,534</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-graph-down me-2 text-danger fs-4"></i> <span class="fw-bold">Taxes</span>
                        </h6>
                        <p id="taxes" class="fs-5 mt-2">KES 26,466</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle me-2 text-warning fs-4"></i> <span class="fw-bold">Pre-Tax
                                Deductions</span>
                        </h6>
                        <p id="pre-tax-deductions" class="fs-5 mt-2">KES 12,325</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-up me-2 text-info fs-4"></i> <span class="fw-bold">Post-Tax
                                Deductions</span>
                        </h6>
                        <p id="post-tax-deductions" class="fs-5 mt-2">KES 0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="container-fluid px-0">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" id="payslipsContainer">
                            {{ loader() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('modals.payslip-details')
        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const payrollId = "{{ $payroll ? $payroll->id : null }}";
                if (payrollId) {
                    getPayslips(1, payrollId);
                }
            });
        </script>
    @endpush

</x-app-layout>
