<x-app-layout>
    <div class="row g-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <div>
                        <input type="text" id="applicationFilter" class="form-control d-inline-block"
                            style="width: 200px;" placeholder="Filter applications...">
                        <select id="jobFilter" class="form-control d-inline-block" style="width: 200px;">
                            <option value="">All Jobs</option>
                            @foreach ($jobPosts as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="locationFilter" class="form-control d-inline-block" style="width: 200px;"
                            placeholder="Filter by location...">
                        <a class="btn btn-secondary btn-sm"
                            href="{{ route('business.applicants.create', $currentBusiness->slug) }}">
                            <i class="bi bi-person-add me-2"></i> Add Applicant
                        </a>
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('business.applications.create', $currentBusiness->slug) }}">
                            <i class="bi bi-plus-square-dotted me-2"></i> Create Job Application
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive" id="jobApplicationsContainer">
                    {{ loader() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @include('modals.schedule-interview')
    <script src="{{ asset('js/main/job-applications.js') }}" type="module"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        $(document).ready(() => {
            getJobApplications();
        });

        function openScheduleInterviewModal(applicationId, applicantName, jobTitle) {
            $('#application_id_input').val(applicationId);
            $('#applicant_name').text(applicantName);
            $('#scheduleInterviewModal').modal('show');
        }
    </script>
    @endpush
</x-app-layout>