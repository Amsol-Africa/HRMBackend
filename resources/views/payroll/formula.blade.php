<x-app-layout>



    <div class="row g-3">
        <div class="col-md-12" id="payrollformulasFormContainer">

            <div class="card">
                <div class="card-body mb-0">
                    {{ loader() }}
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        @include('modals.payroll-formula')
        <script src="{{ asset('js/main/formula.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                loadFormulas()


            });
        </script>
    @endpush

</x-app-layout>
