<x-app-layout title="Create Survey">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Create New Survey</h4>
                        @include('surveys._form')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('js/main/surveys.js') }}" type="module"></script>
    @endpush
</x-app-layout>