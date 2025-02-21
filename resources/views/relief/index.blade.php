<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <a class="btn btn-primary btn-sm"
                        href="{{ route('business.relief.create', $currentBusiness->slug) }}"> <i
                            class="bi bi-plus-square-dotted"></i> Add Reliefs</a>

                </div>
                <div class="card-body">
                    <div class="table-responsive" id="reliefsContainer">
                        <div class="text-center py-4">{{ loader() }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        {{-- @include('modals.payroll-formula') --}}
        <script src="{{ asset('js/main/reliefs.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getReliefs()
            })
        </script>
    @endpush

</x-app-layout>
