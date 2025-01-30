<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $page }}</h5>
                </div>
                <div class="card-body">

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

            })
        </script>
    @endpush

</x-app-layout>
