<x-app-layout>
    <div class="container py-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="display-5 fw-bold text-dark">Key Performance Indicators</h1>
            <div>
                <button class="btn btn-outline-primary btn-sm" onclick="getKPIs()">
                    <i class="fas fa-sync-alt me-2"></i> Refresh
                </button>
            </div>
        </div>

        <!-- KPI Cards Container -->
        <div class="row g-4" id="kpisContainer">
            <!-- Initial Loader -->
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Loading your KPIs...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Custom Styles for Modern Look */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    .container {
        max-width: 1400px;
    }

    .display-5 {
        font-size: 2.5rem;
        letter-spacing: -0.02em;
    }

    .btn-outline-primary {
        border-color: #0d6efd;
        color: #0d6efd;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: white;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/main/kpis.js') }}" type="module"></script>
    <script>
    $(document).ready(() => {
        getKPIs();
    });
    </script>
    @endpush
</x-app-layout>