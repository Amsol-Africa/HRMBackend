<x-app-layout title="Create Role">
    <div class="row g-3">
        <div class="col-12">
            <h5 class="mb-4">Create Role</h5>
            <div id="roleFormContainer">
                @include('roles._form')
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    window.businessSlug = "{{ $currentBusiness->slug }}";
    </script>
    <script src="{{ asset('js/main/roles.js') }}" type="module"></script>
    @endpush
</x-app-layout>