import { btn_loader } from "https://amsol.anzar.co.ke/public_html/js/client/config.js";
import RequestClient from "https://amsol.anzar.co.ke/public_html/js/client/RequestClient.js";
import EmployeesService from "https://amsol.anzar.co.ke/public_html/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

window.getEmployees = async function (page = 1) {
    try {
        let data = {page:page};
        const employeesCards = await employeesService.fetch(data);
        $("#employeesContainer").html(employeesCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("employeesForm"));

    try {
        if (formData.has('employee_id')) {
            await employeesService.update(formData);
        } else {
            await employeesService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};
window.deleteEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const employee = btn.data("employee");
    const data = { employee: employee };

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
                await employeesService.delete(data);
                getEmployees();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
