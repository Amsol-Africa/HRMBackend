<x-app-layout>

    <div class="card">
        <div class="card-body" id="applicationFormContainer">
            @include('job-applications._job_applications_form');
        </div>
    </div>


    @push('scripts')
        <script src="{{ asset('js/main/job-applications.js') }}" type="module"></script>
    @endpush

</x-app-layout>
