import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DepartmentsService from "/js/client/DepartmentsService.js";

const requestClient = new RequestClient();
const departmentsService = new DepartmentsService(requestClient);

window.getDepartments = async function (page = 1) {
    try {
        let data = {page:page};
        const departmentsCards = await departmentsService.fetch(data);
        $("#departmentsContainer").html(departmentsCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveDepartment = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("departmentsForm"));

    try {
        if (formData.has('department_slug')) {
            await departmentsService.update(formData);
        } else {
            await departmentsService.save(formData);
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
        $('#departmentsFormContainer').html(form)
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
