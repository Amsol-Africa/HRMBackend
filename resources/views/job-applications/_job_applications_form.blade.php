<form id="jobApplicationForm" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="applicant_id" class="form-label">Applicant</label>
        <select name="applicant_id" id="applicant_id" class="form-control">
            <option value="">-- Select Applicant --</option>
            @foreach($applicants as $applicant)
                <option value="{{ $applicant->id }}">{{ $applicant->user->name }} - {{ $applicant->user->email }}</option>
            @endforeach
        </select>

        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
            <i class="bi bi-plus-square-dotted me-2"></i> Add Applicant
        </button>
    </div>

    <div class="mb-3">
        <label for="job_post_id" class="form-label">Job Post</label>
        <select name="job_post_id" id="job_post_id" class="form-control">
            @foreach($job_posts as $job)
                <option value="{{ $job->slug }}">{{ $job->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="cover_letter" class="form-label">Cover Letter</label>
        <textarea name="cover_letter" id="cover_letter" class="form-control tinyMce"></textarea>
    </div>

    <div class="mb-3">
        <label for="attachments" class="form-label">File Attachments</label>
        <input type="file" multiple name="attachments[]" id="attachments" class="form-control">
    </div>

    <button type="button" onclick="saveApplication(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle me-2"></i> Submit Application</button>
</form>

<!-- Add Applicant Modal -->
<div class="modal fade" id="addApplicantModal" tabindex="-1" aria-labelledby="addApplicantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addApplicantModalLabel">Add New Applicant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('job-applications._applicant_form')
            </div>
        </div>
    </div>
</div>
