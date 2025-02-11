<div class="modal fade" id="scheduleInterviewModal" tabindex="-1" aria-labelledby="scheduleInterviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleInterviewModalLabel">Schedule Interview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="interviewForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Job Application</label>
                        <select name="application_id" class="form-select" required>
                            <option value="">-- Select Application --</option>
                            @foreach($applications as $application)
                            <option value="{{ $application->id }}">{{ $application->applicant->name }} - {{ $application->jobPost->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Interviewer</label>
                        <select name="interviewer_id" class="form-select">
                            <option value="">-- Select Interviewer --</option>
                            @foreach($interviewers as $interviewer)
                            <option value="{{ $interviewer->id }}">{{ $interviewer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Interview Type</label>
                        <select name="type" class="form-select">
                            <option value="in-person">In-Person</option>
                            <option value="video">Video</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Scheduled Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>

                    <div class="mb-3 location-field d-none">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>

                    <div class="mb-3 meeting-link-field d-none">
                        <label class="form-label">Meeting Link</label>
                        <input type="url" name="meeting_link" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="scheduleInterview(this)" class="btn btn-primary"> <i class="bi bi-check-circle"></i> Save Interview</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        document.querySelector('.location-field').classList.toggle('d-none', this.value !== 'in-person');
        document.querySelector('.meeting-link-field').classList.toggle('d-none', this.value !== 'video');
    });
</script>
