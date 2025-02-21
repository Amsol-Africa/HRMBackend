import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobPostService from "/js/client/JobPostService.js";

const requestClient = new RequestClient();
const jobPostService = new JobPostService(requestClient);

window.getJobPosts = async function (page = 1) {
    try {
        let data = {page:page};
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

    const job_post  = btn.data("job-post");
    const data = { job_post:job_post  };

    try {
        const form = await jobPostService.edit(data);
        $('#jobPostFormContainer').html(form)
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
