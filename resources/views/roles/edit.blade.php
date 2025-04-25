<x-app-layout title="Edit Role">
    <div class="row g-3">
        <div class="col-12">
            <h5 class="mb-4">Edit Role: {{ $role->name }}</h5>
            <div id="roleFormContainer">
                @include('roles._form', ['role' => $role, 'permissions' => $permissions])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.businessSlug = "{{ $role->business->slug }}";
    </script>
    <script src="{{ asset('js/main/roles.js') }}" type="module"></script>
    @endpush
</x-app-layout>