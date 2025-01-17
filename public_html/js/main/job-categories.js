import { btn_loader } from "https://amsol.anzar.co.ke/public_html/js/client/config.js";
import RequestClient from "https://amsol.anzar.co.ke/public_html/js/client/RequestClient.js";
import JobCategoriesService from "https://amsol.anzar.co.ke/public_html/js/client/JobCategoriesService.js";

const requestClient = new RequestClient();
const jobCategoriesService = new JobCategoriesService(requestClient);

window.getJobCategories = async function (page = 1) {
    try {
        let data = {page:page};
        const jobCategoriesCards = await jobCategoriesService.fetch(data);
        $("#jobCategoriesContainer").html(jobCategoriesCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveJobCategory = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("jobCategoriesForm"));

    try {
        if (formData.has('job_category_slug')) {
            await jobCategoriesService.update(formData);
        } else {
            await jobCategoriesService.save(formData);
        }
        getJobCategories();
    } finally {
        btn_loader(btn, false);
    }
};
window.editJobCategory = async function (btn) {
    btn = $(btn);

    const job_category = btn.data("job-category");
    const data = { job_category: job_category };

    try {
        const form = await jobCategoriesService.edit(data);
        $('#jobCategoriesFormContainer').html(form)
    } finally {
    }
};
window.deleteJobCategory = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const jobCategory = btn.data("job-category");
    const data = { job_category: jobCategory };

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
                await jobCategoriesService.delete(data);
                getJobCategories();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
