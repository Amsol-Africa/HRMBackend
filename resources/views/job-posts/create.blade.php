<x-app-layout>

    <div id="jobPostFormContainer">
        @include('job-posts._job_post_form');
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/job-posts.js') }}" type="module"></script>
    @endpush

</x-app-layout>
