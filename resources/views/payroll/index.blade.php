<x-app-layout>

    <div class="row g-20">
        <div class="col-md-12" id="payrollsContainer">
            <div class="card">
                <div class="card-body">
                    {{ loader() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script>

            document.addEventListener('DOMContentLoaded', function () {

                getPayrolls();

            });

        </script>

    @endpush

</x-app-layout>
