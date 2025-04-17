import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DepartmentsService from "/js/client/DepartmentsService.js";

const requestClient = new RequestClient();
const departmentsService = new DepartmentsService(requestClient);

window.getDepartments = async function (page = 1) {
    try {
        let data = { page: page };
        const departmentTable = await departmentsService.fetch(data);
        $("#departmentsContainer").html(departmentTable);

        const exportTitle = "Departments Report";
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
                deleteSelectedDepartments();
            }
        });

        const table = new DataTable('#departmentsTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[3, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 500, 1000], [5, 10, 20, 50, 100, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#departmentsTable tbody').on('click', 'tr', function () {
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
        console.error("Error loading department data:", error);
    }
};

async function deleteSelectedDepartments() {
    let selectedIds = window.getSelectedIds();

    if (selectedIds.length === 0) {
        Swal.fire({
            title: "No Selection",
            text: "Please select at least one department to delete.",
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
                await departmentsService.delete({ ids: selectedIds });

                Swal.fire({
                    title: "Deleted!",
                    text: "Selected departments have been deleted.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                getDepartments();
            } catch (error) {
                console.error("Error deleting departments:", error);

                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong while deleting departments.",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK",
                });
            }
        }
    });
}

function sendEmailReport() {
    const subject = encodeURIComponent("Departments Report");
    const body = encodeURIComponent("Here is the departments report. Please find the attached file or download it from the system.");

    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

window.saveDepartment = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = document.getElementById("departmentsForm");
    let formData = new FormData(form);

    try {
        if (formData.has('department_slug') && formData.get('department_slug')) {
            await departmentsService.update(formData);

            form.reset();
            $("#department_slug").val("");
            $("#department_name").val("");
            $("#description").val("");

            $("#card-header").text("Add New Department");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Department');
            }, 100);

        } else {
            await departmentsService.save(formData);
            $("#department_name").val("");
            $("#description").val("");

            $("#card-header").text("Add New Department");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Department');
            }, 100);
        }

        getDepartments();
    } finally {
        btn_loader(btn, false);
    }
};

window.editDepartment = async function (btn) {
    btn = $(btn);

    const department = btn.data("department");
    const data = { department: department };

    try {
        const form = await departmentsService.edit(data);
        $('#departmentsFormContainer').html(form);

        setTimeout(() => {
            $("#submitButton").html('<i class="bi bi-check-circle"></i> Update Department');
        }, 100);

        $("#card-header").text("Edit Department");
    } finally {
    }
};

window.deleteDepartment = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const department = btn.data("department");
    const data = { department: department };

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
                await departmentsService.delete(data);
                getDepartments();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
