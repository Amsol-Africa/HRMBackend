<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">{{ $page }}</h2>
                    <span id="warningCount"
                        class="badge bg-primary-soft text-primary px-3 py-2">{{ $warnings->count() }} Warnings</span>
                </div>
                <!-- Form Section -->
                <div class="card shadow-sm mb-5 border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Issue New Warning</h4>
                        <div id="warningFormContainer">
                            @include('employees.warning._form')
                        </div>
                    </div>
                </div>

                <!-- Cards Section -->
                <div>
                    <h4 class="fw-semibold text-dark mt-4 mb-4">Current Warnings</h4>
                    <div id="warningsContainer">
                        @include('employees.warning._cards')
                    </div>
                </div>
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
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/main/warnings.js') }}" type="module"></script>
    @endpush
</x-app-layout>