<x-app-layout title="Create Role">
    <div class="row g-3">
        <div class="col-12">
            <h5 class="mb-4">Create Role</h5>
            <div id="roleFormContainer">
                @include('roles._form')
            </div>
            <div class="mt-3">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/roles.js') }}" type="module"></script>
    @endpush
</x-app-layout>