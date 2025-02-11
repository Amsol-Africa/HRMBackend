<x-app-layout>

    <div id="jobPostFormContainer">
        @include('job-applications._job_applications_form');
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/job-applications.js') }}" type="module"></script>
    @endpush

</x-app-layout>
