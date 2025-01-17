<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <a class="btn btn-primary btn-sm" href="{{ route('business.deductions.create', $currentBusiness->slug) }}"> <i class="bi bi-plus-square-dotted"></i> Add Deductions</a>

                </div>
                <div class="card-body" id="deductionsContainer">

                    {{ loader() }}

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        {{-- @include('modals.payroll-formula') --}}
        <script src="{{ asset('js/main/deductions.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getDeductions()
            })
        </script>
    @endpush

</x-app-layout>
