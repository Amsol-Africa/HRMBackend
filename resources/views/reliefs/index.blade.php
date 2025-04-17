<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">{{ $page }}</h2>
                    <span id="reliefCount" class="badge bg-primary-soft text-primary px-3 py-2">{{ $reliefs->count() }}
                        Reliefs</span>
                </div>
                <div class="card shadow-sm mb-5 border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Create New Relief</h4>
                        <div id="reliefFormContainer">
                            @include('reliefs._form')
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="fw-semibold text-dark mt-4 mb-4">Current Reliefs</h4>
                    <div id="reliefsContainer">
                        @include('reliefs._table')
                    </div>
                </div>
                <div id="reliefModalContainer"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/reliefs.js') }}" type="module"></script>
    @endpush
</x-app-layout>