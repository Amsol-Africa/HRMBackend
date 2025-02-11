<div class="modal fade" id="scheduleInterviewModal" tabindex="-1" aria-labelledby="scheduleInterviewModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleInterviewModalLabel">Schedule Interview for <span id="applicant_name"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="interviewsForm">
                @csrf

                <input type="text" name="application_id" hidden id="application_id_input" class="form-control" required>

                <div class="modal-body">
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
                        <input type="datetime-local" name="scheduled_at" class="form-control datepicker" required>
                    </div>

                    <div class="mb-3 place-field d-none">
                        <label class="form-label">Location</label>
                        <input type="text" name="place" class="form-control">
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
                    <button type="button" onclick="scheduleInterview(this)" class="btn btn-primary"> <i class="bi bi-check-circle me-2"></i> Save Interview</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        document.querySelector('.place-field').classList.toggle('d-none', this.value !== 'in-person');
        document.querySelector('.meeting-link-field').classList.toggle('d-none', this.value !== 'video');
    });
</script>
