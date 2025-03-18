import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobPostService from "/js/client/JobPostService.js";

const requestClient = new RequestClient();
const jobPostService = new JobPostService(requestClient);

// Optionally keep TinyMce if used elsewhere
function loadTinyMce() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea[name="description"].tinyMce',
            plugins: 'lists link image table',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent',
            setup: (editor) => {
                editor.on('init', () => {
                    console.log('TinyMCE initialized for editor:', editor.id);
                });
            }
        });
    } else {
        console.error('TinyMCE script not loaded. Include <script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/6/tinymce.min.js"></script>');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // loadTinyMce(); // Uncomment if TinyMCE is still needed elsewhere

    const generateAiButton = document.querySelector('[data-bs-target="#aiModal"]');
    const aiContentDiv = document.getElementById('aiGeneratedContent');
    const useAiContentBtn = document.getElementById('useAiContent');

    if (generateAiButton) {
        generateAiButton.addEventListener('click', async function () {
            console.log('AI Generate button clicked');

            const form = document.getElementById('jobPostForm');
            const title = form.querySelector('[name="title"]').value;
            const employmentType = form.querySelector('[name="employment_type"]').value;
            const place = form.querySelector('[name="place"]').value;
            const salaryRange = form.querySelector('[name="salary_range"]').value;

            if (!title || !employmentType || !place) {
                aiContentDiv.innerHTML = '<div class="alert alert-warning">Please fill in Job Title, Employment Type, and Location first.</div>';
                return;
            }

            aiContentDiv.innerHTML = '<div class="text-muted">Generating...</div>';

            try {
                const response = await requestClient.post('/generate-job-description', {
                    title,
                    employment_type: employmentType,
                    place,
                    salary_range: salaryRange
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const responseText = await response.text();
                console.log('Raw response text:', responseText);
                const jsonResponse = JSON.parse(responseText);
                console.log('Parsed JSON response:', jsonResponse);

                // Fix: Check jsonResponse.status, not message
                if (jsonResponse.message === 'success' && jsonResponse.data && jsonResponse.data.description) {
                    aiContentDiv.innerHTML = jsonResponse.data.description;
                } else {
                    aiContentDiv.innerHTML = '<div class="alert alert-danger">Error: ' + (jsonResponse.message || 'Unknown error') + '</div>';
                }
            } catch (error) {
                aiContentDiv.innerHTML = '<div class="alert alert-danger">Error generating description: ' + error.message + '</div>';
                console.error('AI Generation Error:', error);
            }
        });
    } else {
        console.warn('AI Generate button not found');
    }

    if (useAiContentBtn) {
        useAiContentBtn.addEventListener('click', function () {
            const textarea = document.querySelector('textarea[name="description"]');
            const content = aiContentDiv.innerHTML;

            if (textarea) {
                // Method 1: Direct value assignment
                if (typeof tinymce !== 'undefined' && tinymce.get(textarea.id)) {
                    tinymce.get(textarea.id).setContent(content);
                    console.log('AI content injected into TinyMCE editor');
                } else {
                    textarea.value = content;
                    console.log('AI content injected into textarea');
                }

                console.log('HTML directly injected into textarea');

                // Method 2: Simulate copy-paste with Clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(content)
                        .then(() => {
                            console.log('Content copied to clipboard');
                            textarea.focus();
                            document.execCommand('paste'); // Attempt paste (may not work in all browsers)
                            console.log('Attempted clipboard paste into textarea');
                        })
                        .catch(err => {
                            console.warn('Clipboard API failed:', err);
                            // Fallback if Clipboard API fails
                            simulatePaste(textarea, content);
                        });
                } else {
                    // Fallback for browsers without Clipboard API support
                    console.warn('Clipboard API not available');
                    simulatePaste(textarea, content);
                }
            } else {
                console.error('Textarea not found');
            }
            bootstrap.Modal.getInstance(document.getElementById('aiModal')).hide();
        });
    } else {
        console.warn('Use AI Content button not found');
    }
});

// Fallback function to simulate paste
function simulatePaste(textarea, content) {
    textarea.focus();
    textarea.value = content; // Ensure content is set
    const event = new Event('input', { bubbles: true });
    textarea.dispatchEvent(event); // Trigger input event for form validation
    console.log('Simulated paste into textarea');
}

// Existing functions unchanged
window.getJobPosts = async function (page = 1) {
    try {
        let data = { page: page };
        const JobPosts = await jobPostService.fetch(data);
        $("#jobPostsContainer").html(JobPosts);
        new DataTable('#jobPostsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.saveJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    tinymce.triggerSave();

    let formData = new FormData(document.getElementById("jobPostForm"));

    try {
        if (formData.has('job_post_slug')) {
            await jobPostService.update(formData);
        } else {
            await jobPostService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};

window.editJobPost = async function (btn) {
    btn = $(btn);

    const job_post = btn.data("job-post");
    const data = { job_post: job_post };

    try {
        const form = await jobPostService.edit(data);
        $('#jobPostFormContainer').html(form);
    } finally {
    }
};

window.deleteJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const job_post = btn.data("job-post");
    const data = { job_post: job_post };

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await jobPostService.delete(data);
                getJobPosts();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};