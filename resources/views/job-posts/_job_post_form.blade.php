<form action="" method="POST" id="jobPostForm">
    <div class="row g-2">
        <div class="col-md-8">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Job Description</h5>
                    <button type="button" id="startTour" class="btn btn-outline-info btn-sm">
                        <i class="fa-solid fa-circle-info me-1"></i> Help
                    </button>
                </div>

                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#aiModal">
                        <i class="fa-solid fa-robot me-1"></i> Generate with AI
                    </button>
                </div>
                <p class="text-muted small">
                    <i class="fa-solid fa-circle-info me-1"></i> AI-generated content may not always be accurate. Please
                    proofread and make necessary changes.
                </p>

                <textarea class="form-control tinyMce" name="description"
                    required>@isset($job_post){{ $job_post->description }}@endisset</textarea>
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
                        <input type="text" class="form-control" placeholder="Job Opening title" name="title"
                            value="@isset($job_post){{ $job_post->title }}@endisset" required>
                    </div>

                    <div class="mb-3">
                        <label for="employment_type" class="form-label">Employment Type</label>
                        <select class="form-select" name="employment_type" required>
                            <option value="full-time" @isset($job_post) @if($job_post->employment_type == 'full-time')
                                selected @endif @endisset>Full-Time</option>
                            <option value="part-time" @isset($job_post) @if($job_post->employment_type == 'part-time')
                                selected @endif @endisset>Part-Time</option>
                            <option value="contract" @isset($job_post) @if($job_post->employment_type == 'contract')
                                selected @endif @endisset>Contract</option>
                            <option value="internship" @isset($job_post) @if($job_post->employment_type == 'internship')
                                selected @endif @endisset>Internship</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="place" class="form-label">Location</label>
                        <input type="text" class="form-control" placeholder="place" name="place"
                            value="@isset($job_post){{ $job_post->place }}@endisset" required>
                    </div>

                    <div class="mb-3">
                        <label for="salary_range" class="form-label">Salary Range (Monthly)</label>
                        <input type="text" class="form-control" placeholder="e.g. 30000 - 50000" name="salary_range"
                            value="@isset($job_post){{ $job_post->salary_range }}@endisset">
                    </div>

                    <button type="button" onclick="saveJobPost(this)" class="btn btn-primary w-100">
                        <i class="fa-regular fa-check-circle me-1"></i> Create Job Opening
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Generation Modal -->
    <div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiModalLabel">AI Generated Job Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="aiGeneratedContent" class="p-3 border rounded"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="useAiContent">Use Description</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("startTour").addEventListener("click", function() {
        const intro = introJs();
        intro.setOptions({
            steps: [{
                    element: "#jobPostForm",
                    title: "Job Posting Form",
                    intro: "Fill in the necessary details to create a job post."
                },
                {
                    element: "button[data-bs-target='#aiModal']",
                    title: "Generate with AI",
                    intro: "Click this button to generate a job description using AI.",
                    position: "right",
                    onbeforechange: function() {
                        // Open the AI modal automatically
                        let aiModal = new bootstrap.Modal(document.getElementById(
                            "aiModal"));
                        aiModal.show();
                    }
                },
                {
                    element: ".btn-primary.w-100",
                    title: "Submit Job Post",
                    intro: "Click to submit the job post after reviewing all details."
                },
                {
                    title: "Tour Completed 🎉",
                    intro: "That's it! You're now ready to create job postings easily."
                }
            ],
            showProgress: true,
            showBullets: false,
            exitOnOverlayClick: false,
            exitOnEsc: true,
            nextLabel: "Next",
            prevLabel: "Back",
            doneLabel: "Finish"
        });

        intro.start();
    });
});
</script>