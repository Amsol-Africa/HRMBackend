import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobApplicantService from "/js/client/JobApplicantService.js";

const requestClient = new RequestClient();
const jobApplicantService = new JobApplicantService(requestClient);

window.getJobApplicants = async function (page = 1) {
    try {
        let data = {page:page};
        const JobApplicants = await jobApplicantService.fetch(data);
        $("#jobApplicantsContainer").html(JobApplicants);
        new DataTable('#jobApplicantsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveApplicant = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("applicantForm"));

    try {
        if (formData.has('_period_slug')) {
            await jobApplicantService.update(formData);
        } else {
            await jobApplicantService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};
window.editJobApplicant = async function (btn) {
    btn = $(btn);

    const job_applicant  = btn.data("job-applicant");
    const data = { job_applicant:job_applicant  };

    try {
        const form = await jobApplicantService.edit(data);
        $('#jobApplicantFormContainer').html(form)
    } finally {
    }
};
window.deleteJobApplicant = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const job_applicant = btn.data("job-applicant");
    const data = { job_applicant: job_applicant };

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
                await jobApplicantService.delete(data);
                getJobApplicants();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
