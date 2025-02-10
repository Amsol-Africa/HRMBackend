<x-app-layout>

    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6><i class="bi bi-calendar-week"></i> Period</h6>
                    <p>January 2024</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6><i class="bi bi-calendar-check"></i> Pay Day</h6>
                    <p>31st January 2024</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6><i class="bi bi-people"></i> Employees</h6>
                    <p>50 Employees</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6><i class="bi bi-cash-stack"></i> Payroll Cost</h6>
                    <p>KES 5,000,000</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6><i class="bi bi-wallet2"></i> Net Pay</h6>
                    <p>KES 3,500,000</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6><i class="bi bi-graph-down"></i> Taxes</h6>
                    <p>KES 1,000,000</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h6><i class="bi bi-arrow-down-circle"></i> Pre-Tax Deductions</h6>
                    <p>KES 300,000</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light text-dark">
                <div class="card-body">
                    <h6><i class="bi bi-arrow-down-up"></i> Post-Tax Deductions</h6>
                    <p>KES 200,000</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" id="payslipsContainer">
                    {{ loader() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('modals.payslip-details')
        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const payrollId = "{{ $payroll ? $payroll->id : null }}";
                if (payrollId) {
                    getPayslips(1, payrollId);
                }

            });

        </script>

    @endpush

</x-app-layout>
