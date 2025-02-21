<form action="" method="POST" id="jobPostForm">
    <div class="row g-2">

        <div class="col-md-8">

            <div class="mb-3">
                <h5 class="mb-2">Job Description</h5>
                <textarea class="form-control tinyMce" name="description" required>@isset($job_post){{ $job_post->description }}@endisset</textarea>
            </div>

        </div>

        <div class="col-md-4">

            <div class="card">
                <div class="card-body">
                    @csrf

                    @isset($job_post)
                        <input type="hidden" name="job_post_slug" value="{{ $job_post->slug }}">
                    @endisset

                    <div class="mb-3">
                        <label for="title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" placeholder="Job Opening title" name="title" value="@isset($job_post){{ $job_post->title }}@endisset" required>
                    </div>

                    <div class="mb-3">
                        <label for="employment_type" class="form-label">Employment Type</label>
                        <select class="form-select" name="employment_type" required>
                            <option value="full-time" @isset($job_post) @if($job_post->employment_type == 'full-time') selected @endif @endisset>Full-Time</option>
                            <option value="part-time" @isset($job_post) @if($job_post->employment_type == 'part-time') selected @endif @endisset>Part-Time</option>
                            <option value="contract" @isset($job_post) @if($job_post->employment_type == 'contract') selected @endif @endisset>Contract</option>
                            <option value="internship" @isset($job_post) @if($job_post->employment_type == 'internship') selected @endif @endisset>Internship</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="place" class="form-label">Location</label>
                        <input type="text" class="form-control" placeholder="place" name="place" value="@isset($job_post){{ $job_post->place }}@endisset" required>
                    </div>

                    <div class="mb-3">
                        <label for="salary_range" class="form-label">Salary Range (Monthly) </label>
                        <input type="text" class="form-control" placeholder="e.g. 30000 - 50000" name="salary_range" value="@isset($job_post){{ $job_post->salary_range }}@endisset">
                    </div>

                    <button type="button" onclick="saveJobPost(this)" class="btn btn-primary w-100"> <i class="fa-regular fa-check-circle me-1"></i> Create Job Opening</button>
                </div>
            </div>

        </div>

    </div>

</form>
