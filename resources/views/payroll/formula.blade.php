<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-body" id="payrollformulasContainer">

                    {{ loader() }}

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        @include('modals.payroll-formula')
        <script src="{{ asset('js/main/formula.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getPayrollFormulas()
            })
        </script>
    @endpush

</x-app-layout>
