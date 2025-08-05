<x-app-layout>
    <div class="card">
        <div class="card-body" id="applicationFormContainer">
            @include('applications._form')
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/job-applications.js') }}" type="module"></script>
    <script>
    const csrfToken = '{{ csrf_token() }}';
    window.businessSlug = '{{ $currentBusiness->slug }}';
    </script>
    @endpush
</x-app-layout>