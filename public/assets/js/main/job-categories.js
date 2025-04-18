import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobCategoriesService from "/js/client/JobCategoriesService.js";

const requestClient = new RequestClient();
const jobCategoriesService = new JobCategoriesService(requestClient);

window.getJobCategories = async function (page = 1) {
    try {
        let data = { page: page };
        const jobCategoriesTable = await jobCategoriesService.fetch(data);
        $("#jobCategoriesContainer").html(jobCategoriesTable);

        const exportTitle = "Job Categories Report";
        const exportButtons = [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type === 'copy' ? 'primary' : type === 'csv' ? 'secondary' : type === 'excel' ? 'success' : type === 'pdf' ? 'danger' : 'info'}`,
            title: exportTitle,
            exportOptions: {
                columns: ':not(:last-child)'
            }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () {
                sendEmailReport();
            }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () {
                deleteSelectedJobCategories();
            }
        });

        const table = new DataTable('#jobCategoriesTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[3, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 500, 1000], [5, 10, 20, 50, 100, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#jobCategoriesTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).find('.row-id').data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedIds = function () {
            return selectedIds;
        };

    } catch (error) {
        console.error("Error loading job categories:", error);
    }
};

async function deleteSelectedJobCategories() {
    let selectedIds = window.getSelectedIds();

    if (selectedIds.length === 0) {
        Swal.fire({
            title: "No Selection",
            text: "Please select at least one job category to delete.",
            icon: "info",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete them!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await jobCategoriesService.delete({ ids: selectedIds });

                Swal.fire({
                    title: "Deleted!",
                    text: "Selected job categories have been deleted.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                getJobCategories();
            } catch (error) {
                console.error("Error deleting job categories:", error);

                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong while deleting job categories.",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK",
                });
            }
        }
    });
}

function sendEmailReport() {
    const subject = encodeURIComponent("Job Categories Report");
    const body = encodeURIComponent("Here is the job categories report. Please find the attached file or download it from the system.");

    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

window.saveJobCategory = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = document.getElementById("jobCategoriesForm");
    let formData = new FormData(form);

    try {
        if (formData.has('job_category_slug') && formData.get('job_category_slug')) {
            await jobCategoriesService.update(formData);

            form.reset();
            $("#job_category_slug").val("");
            $("#job_category").val("");
            $("#description").val("Enter some description...");

            $("#card-header").text("Add New Job Category");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Job Category');
            }, 100);

        } else {
            await jobCategoriesService.save(formData);
            form.reset();
            $("#job_category_slug").val("");
            $("#job_category").val("");
            $("#description").val("Enter some description...");

            $("#card-header").text("Add New Job Category");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Job Category');
            }, 100);
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
        $('#jobCategoriesFormContainer').html(form);

        setTimeout(() => {
            $("#submitButton").html('<i class="bi bi-check-circle"></i> Update Job Category');
        }, 100);

        $("#card-header").text("Edit Job Category");
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
