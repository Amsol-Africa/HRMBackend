<form id="surveyForm" class="needs-validation" novalidate action="{{ route('surveys.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <label for="title" class="form-label fw-medium text-dark">Survey Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label fw-medium text-dark">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="access_type" class="form-label fw-medium text-dark">Access Type</label>
            <select name="access_type" id="access_type" class="form-select" required>
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="employee_only">Employee Only</option>
                <option value="client_only">Client Only</option>
            </select>
            @error('access_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="status" class="form-label fw-medium text-dark">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="closed">Closed</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="start_date" class="form-label fw-medium text-dark">Start Date (Optional)</label>
            <input type="date" name="start_date" id="start_date" class="form-control">
            @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="end_date" class="form-label fw-medium text-dark">End Date (Optional)</label>
            <input type="date" name="end_date" id="end_date" class="form-control">
            @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div id="questionsContainer" class="mt-4">
        <h5 class="fw-semibold text-dark">Questions</h5>
        <div class="question-block mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium text-dark">Question Text</label>
                    <input type="text" name="questions[0][question_text]" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium text-dark">Question Type</label>
                    <select name="questions[0][question_type]" class="form-select question-type" required>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="rating">Rating</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-check-label">
                        <input type="checkbox" name="questions[0][is_required]" value="1" class="form-check-input">
                        Required
                    </label>
                </div>
                <div class="col-12 options-container" style="display: none;">
                    <label class="form-label fw-medium text-dark">Options</label>
                    <div class="options-list">
                        <div class="input-group mb-2">
                            <input type="text" name="questions[0][options][0][text]" class="form-control">
                            <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                        </div>
                        <div class="input-group mb-2">
                            <input type="text" name="questions[0][options][1][text]" class="form-control">
                            <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary add-option">Add Option</button>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger remove-question mt-2">Remove Question</button>
        </div>
    </div>
    <button type="button" class="btn btn-outline-primary add-question mt-3">Add Question</button>

    <div class="mt-4">
        <button type="button" class="btn btn-primary btn-modern" onclick="saveSurvey(this)">
            <i class="fa fa-save me-2"></i> Create Survey
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('surveyForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
            } else {
                saveSurvey(form.querySelector('button[type="button"]'));
            }
        });

        // Add question
        document.querySelector('.add-question').addEventListener('click', () => {
            const questionsContainer = document.getElementById('questionsContainer');
            const questionCount = questionsContainer.querySelectorAll('.question-block').length;
            const questionBlock = document.createElement('div');
            questionBlock.className = 'question-block mb-3';
            questionBlock.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark">Question Text</label>
                            <input type="text" name="questions[${questionCount}][question_text]" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark">Question Type</label>
                            <select name="questions[${questionCount}][question_type]" class="form-select question-type" required>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="rating">Rating</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-check-label">
                                <input type="checkbox" name="questions[${questionCount}][is_required]" value="1" class="form-check-input">
                                Required
                            </label>
                        </div>
                        <div class="col-12 options-container" style="display: none;">
                            <label class="form-label fw-medium text-dark">Options</label>
                            <div class="options-list">
                                <div class="input-group mb-2">
                                    <input type="text" name="questions[${questionCount}][options][0][text]" class="form-control">
                                    <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" name="questions[${questionCount}][options][1][text]" class="form-control">
                                    <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary add-option">Add Option</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger remove-question mt-2">Remove Question</button>
                `;
            questionsContainer.appendChild(questionBlock);
            attachEventListeners(questionBlock);
        });

        // Attach event listeners to existing blocks
        document.querySelectorAll('.question-block').forEach(block => attachEventListeners(block));

        function attachEventListeners(block) {
            // Toggle options visibility based on question type
            const questionTypeSelect = block.querySelector('.question-type');
            const optionsContainer = block.querySelector('.options-container');
            questionTypeSelect.addEventListener('change', () => {
                optionsContainer.style.display = questionTypeSelect.value === 'multiple_choice' ? 'block' :
                    'none';
            });

            // Add option
            block.querySelector('.add-option')?.addEventListener('click', () => {
                const optionsList = block.querySelector('.options-list');
                const questionIndex = block.querySelector('input[name*="[question_text]"]').name.match(
                    /questions\[(\d+)\]/)[1];
                const optionCount = optionsList.querySelectorAll('.input-group').length;
                const optionInput = document.createElement('div');
                optionInput.className = 'input-group mb-2';
                optionInput.innerHTML = `
                        <input type="text" name="questions[${questionIndex}][options][${optionCount}][text]" class="form-control">
                        <button type="button" class="btn btn-outline-danger remove-option">Remove</button>
                    `;
                optionsList.appendChild(optionInput);
                optionInput.querySelector('.remove-option').addEventListener('click', () => {
                    if (optionsList.querySelectorAll('.input-group').length > 2) {
                        optionInput.remove();
                    }
                });
            });

            // Remove option
            block.querySelectorAll('.remove-option').forEach(button => {
                button.addEventListener('click', () => {
                    const optionsList = block.querySelector('.options-list');
                    if (optionsList.querySelectorAll('.input-group').length > 2) {
                        button.closest('.input-group').remove();
                    }
                });
            });

            // Remove question
            block.querySelector('.remove-question')?.addEventListener('click', () => {
                if (document.querySelectorAll('.question-block').length > 1) {
                    block.remove();
                }
            });
        }
    });
</script>
@endpush