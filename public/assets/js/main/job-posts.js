import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', function () {
    const generateAiButton = document.querySelector('[data-bs-target="#aiModal"]');
    const aiContentDiv = document.getElementById('aiGeneratedContent');
    const useAiContentBtn = document.getElementById('useAiContent');

    if (generateAiButton) {
        generateAiButton.addEventListener('click', async function () {
            const form = document.getElementById('jobPostForm');
            const title = form.querySelector('[name="title"]').value;
            const employmentType = form.querySelector('[name="employment_type"]').value;
            const place = form.querySelector('[name="place"]').value || form.querySelector('[name="location_id"]').value;
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
                });
                if (response.message === 'success' && response.data.description) {
                    aiContentDiv.innerHTML = response.data.description;
                } else {
                    aiContentDiv.innerHTML = '<div class="alert alert-danger">Error: ' + (response.message || 'Unknown error') + '</div>';
                }
            } catch (error) {
                aiContentDiv.innerHTML = '<div class="alert alert-danger">Error generating description: ' + error.message + '</div>';
            }
        });
    }

    if (useAiContentBtn) {
        useAiContentBtn.addEventListener('click', function () {
            const textarea = document.querySelector('textarea[name="description"]');
            textarea.value = aiContentDiv.innerHTML;
            bootstrap.Modal.getInstance(document.getElementById('aiModal')).hide();
        });
    }
});

window.getJobPosts = async function () {
    try {
        const response = await requestClient.post('/job-posts/fetch', {});
        $("#jobPostsContainer").html(response.data.html);
        new DataTable('#jobPostsTable', { responsive: true });
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load job posts.', 'error');
    }
};

window.saveJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData(document.getElementById("jobPostForm"));

    try {
        if (formData.has('job_post_id')) {
            await requestClient.post(`/job-posts/${formData.get('job_post_id')}/update`, formData);
            Swal.fire('Success!', 'Job post updated.', 'success');
            window.location.href = '{{ route("business.recruitment.jobs.index", $currentBusiness->slug) }}';
        } else {
            await requestClient.post('/job-posts/store', formData);
            Swal.fire('Success!', 'Job post created.', 'success');
            window.location.href = '{{ route("business.recruitment.jobs.index", $currentBusiness->slug) }}';
        }
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to save job post.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editJobPost = async function (btn) {
    btn = $(btn);
    const jobPostId = btn.data("job-post");

    try {
        const response = await requestClient.post('/job-posts/edit', { job_post_id: jobPostId });
        $('#jobPostFormContainer').html(response.data);
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deleteJobPost = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const jobPostId = btn.data("job-post");

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
                await requestClient.post(`/job-posts/${jobPostId}/destroy`, { job_post_id: jobPostId });
                Swal.fire('Deleted!', 'Job post deleted.', 'success');
                getJobPosts();
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};