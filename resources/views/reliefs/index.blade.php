<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">{{ $page }}</h2>
                    <span id="reliefCount" class="badge bg-primary-soft text-primary px-3 py-2">{{ $reliefs->count() }}
                        Reliefs</span>
                </div>
                <!-- Form Section -->
                <div class="card shadow-sm mb-5 border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Create New Relief</h4>
                        <div id="reliefFormContainer">
                            @include('reliefs._form')
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div>
                    <h4 class="fw-semibold text-dark mt-4 mb-4">Current Reliefs</h4>
                    <div id="reliefsContainer">
                        @include('reliefs._table')
                    </div>
                </div>

                <!-- Modal Container -->
                <div id="reliefModalContainer"></div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .bg-primary-soft {
        background-color: #e7f1ff;
    }

    .btn-modern {
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/main/reliefs.js') }}" type="module"></script>
    @endpush
</x-app-layout>