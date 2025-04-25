<x-app-layout title="Roles Management">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between text-white">
                    <h5 class="mb-0">Roles Management</h5>
                    <a href="{{ route('business.roles.create', $currentBusiness->slug) }}"
                        class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-square-dotted me-2"></i> Add Role
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="roleFilter" class="form-control" placeholder="Filter by name...">
                    </div>
                    <div id="rolesContainer">
                        {{ loader() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/roles.js') }}" type="module"></script>
    <script>
    $(document).ready(() => getRoles());
    </script>
    @endpush
</x-app-layout>