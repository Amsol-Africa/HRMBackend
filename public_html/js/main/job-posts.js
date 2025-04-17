import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobPostService from "/js/client/JobPostService.js";

const requestClient = new RequestClient();
const jobPostService = new JobPostService(requestClient);

document.addEventListener('DOMContentLoaded', () => {
    const generateAiButton = document.querySelector('[data-bs-target="#aiModal"]');
    const aiContentDiv = document.getElementById('aiGeneratedContent');
    const useAiContentBtn = document.getElementById('useAiContent');
    const filterInput = document.getElementById('jobFilter');
    const jobPostsContainer = document.getElementById('jobPostsContainer');

    if (document.getElementById('jobPostForm')) {
        loadTinyMce();
    }

    if (jobPostsContainer) {
        getJobPosts();
    }

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
        useAiContentBtn.addEventListener('click', () => {
            const textarea = document.querySelector('textarea[name="description"]');
            const content = aiContentDiv.innerHTML;
            if (textarea && tinymce.get(textarea.id)) {
                tinymce.get(textarea.id).setContent(content);
            } else if (textarea) {
                textarea.value = content;
            }
            bootstrap.Modal.getInstance(document.getElementById('aiModal'))?.hide();
        });
    }

    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getJobPosts(1, filterInput.value);
        }, 300));
    }

    const form = document.getElementById('jobPostForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            saveJobPost(document.querySelector('button[type="button"][onclick^="saveJobPost"]'));
        });
    }
});

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

window.getJobPosts = async function (page = 1, filter = '') {
    const container = $("#jobPostsContainer");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading job posts...</div>');
        const data = { page, filter };
        const response = await jobPostService.fetch(data);
        if (typeof response === 'string') {
            container.html(response);
        } else if (response && response.data) {
            container.html(response.data);
        } else {
            throw new Error('Invalid response format from server');
        }

        if ($('#jobPostsTable').length) {
            if ($.fn.DataTable.isDataTable('#jobPostsTable')) {
                $('#jobPostsTable').DataTable().destroy();
            }

            $('#jobPostsTable').DataTable({
                responsive: true,
                order: [[4, 'desc']],
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: "No job posts available",
                    loadingRecords: "Loading..."
                }
            });
        }
    } catch (error) {
        console.error("Error loading job posts:", error);
        container.html(`<div class="alert alert-danger">Error loading job posts: ${error.message}</div>`);
        toastr.error('Failed to load job posts: ' + error.message, "Error");
    }
};

window.saveJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    tinymce.triggerSave();

    const formData = new FormData(document.getElementById("jobPostForm"));
    try {
        if (formData.has('job_post_slug')) {
            await jobPostService.update(formData);
            // toastr.success("Job post updated successfully!", "Success");
        } else {
            await jobPostService.save(formData);
            // toastr.success("Job post created successfully!", "Success");
        }
        const businessSlug = window.businessSlug || 'default';
        setTimeout(() => {
            window.location.href = `/business/${businessSlug}/recruitment/job-posts`;
        }, 1500);
    } catch (error) {
        toastr.error('Failed to save job post: ' + error.message, "Error");
    } finally {
        btn_loader(btn, false);
    }
};

window.editJobPost = async function (btn) {
    btn = $(btn);
    const job_post = btn.data("job-post");
    try {
        const response = await jobPostService.edit({ job_post });
        $('#jobPostFormContainer').html(response.data);
        loadTinyMce();
    } catch (error) {
        console.error('Error loading edit form:', error);
        toastr.error('Failed to load edit form: ' + error.message, "Error");
    }
};

window.togglePublic = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const job_post = btn.data("job-post");
    try {
        const response = await jobPostService.togglePublic({ job_post });
        await getJobPosts();
        // toastr.success(response.message || 'Job post visibility toggled successfully!', "Success");
    } catch (error) {
        toastr.error('Failed to toggle visibility: ' + error.message, "Error");
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const job_post = btn.data("job-post");

    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await jobPostService.delete({ job_post });
                await getJobPosts();
                // toastr.success(response.message || 'Job post has been deleted.', "Success");
            } catch (error) {
                toastr.error('Failed to delete job post: ' + error.message, "Error");
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

function loadTinyMce() {
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE is not loaded yet. Ensure the script is included in app.blade.php.');
        toastr.error('TinyMCE editor could not be loaded. Please check your setup.', "Error");
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.1/tinymce.min.js';
        script.onload = () => {
            console.log('TinyMCE script loaded dynamically');
            initializeTinyMce();
        };
        script.onerror = () => {
            console.error('Failed to load TinyMCE script dynamically');
        };
        document.head.appendChild(script);
    } else {
        initializeTinyMce();
    }
}

function initializeTinyMce() {
    tinymce.remove('.tinyMce');
    tinymce.init({
        selector: 'textarea.tinyMce',
        height: 600,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        content_css: '//www.tiny.cloud/css/codepen.min.css',
        skin: 'oxide',
        content_style: 'body { font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:1.6; }',
        setup: function (editor) {
            editor.on('init', function () {
                console.log('TinyMCE initialized for editor:', editor.id);
            });
            editor.on('error', function (e) {
                console.error('TinyMCE error:', e);
                toastr.error('TinyMCE initialization error: ' + e.message, "Error");
            });
        }
    }).then(() => {
        console.log('TinyMCE initialized successfully');
    }).catch(error => {
        console.error('TinyMCE initialization failed:', error);
        toastr.error('Failed to initialize TinyMCE: ' + error.message, "Error");
    });
}