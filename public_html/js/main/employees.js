import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

window.getEmployees = async function (page = 1, status = null) {
    try {
        let data = {page:page, status:status};
        const employeesCards = await employeesService.fetch(data);
        $('#statusInput').val(status)
        $(`#${status}Container`).html(employeesCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.searchEmployees = async function (btn) {
    let name = document.getElementById('employeeName').value;
    let employee_no = document.getElementById('employeeNo').value;
    let department = document.getElementById('employeeDepartment').value;
    let location = document.getElementById('location').value;
    let gender = document.getElementById('employeeGender').value;
    let status = localStorage.getItem('employeeStatus') || 'active';

    try {
        let data = {
            page:1,
            status:status,
            name : name,
            employee_no : employee_no,
            department : department,
            location : location,
            gender : gender,
        };
        const employeesCards = await employeesService.fetch(data);
        $(`#${status}Container`).html(employeesCards);
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
