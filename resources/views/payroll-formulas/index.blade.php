<x-app-layout title="{{ $page }}">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm mb-5 border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4" id="formulaFormTitle">Create New Payroll Formula</h4>
                        <div id="formulaFormContainer">
                            @include('payroll-formulas._form', ['countries' => $countries])
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="fw-semibold text-dark mt-4 mb-4">Current Payroll Formulas</h4>
                    <div id="formulasContainer">
                        @include('payroll-formulas._table', ['formulas' => $formulas])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.businessSlug = '{{ $business->slug }}';
    </script>
    <script src="{{ asset('js/main/payroll-formulas.js') }}" type="module"></script>
    @endpush
</x-app-layout>