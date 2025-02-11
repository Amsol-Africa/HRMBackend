<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>

                    <div>
                        <a class="btn btn-secondary btn-sm" href="{{ route('business.job-applications.applicants.create', $currentBusiness->slug) }}"> <i class="bi bi-person-add me-2"></i> Add Applicant</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('business.job-applications.create', $currentBusiness->slug) }}"> <i class="bi bi-plus-square-dotted me-2"></i> Create Job Application</a>
                    </div>

                </div>
                <div class="card-body" id="jobApplicantsContainer">

                    {{ loader() }}

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        {{-- @include('modals.payroll-formula') --}}
        <script src="{{ asset('js/main/job-applicants.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getJobApplicants()
            })
        </script>
    @endpush

</x-app-layout>
