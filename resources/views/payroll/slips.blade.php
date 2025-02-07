<x-app-layout>

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
