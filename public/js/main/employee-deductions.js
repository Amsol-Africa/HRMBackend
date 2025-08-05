import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeeDeductionsService from "/js/client/EmployeeDeductionsService.js";

const requestClient = new RequestClient();
const employeeDeductionsService = new EmployeeDeductionsService(requestClient);

window.getEmployeeDeductions = async function (page = 1) {
    try {
    } catch (error) {
        console.error("Error loading employee deductions data:", error);
    }
};

window.loadEmployeeDeductions = async function () {
    let data = {};

    try {
        const employeeDeductionsForm = await employeeDeductionsService.create(data);
        $("#employeeDeductionsFormContainer").html(employeeDeductionsForm);
    } finally {
    }
};

window.saveEmployeeDeduction = async function (data) {
    try {
        await employeeDeductionsService.save(data);
        getEmployeeDeductions();
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteEmployeeDeduction = async function (data, row) {
    try {
        await employeeDeductionsService.delete(data);
        row.remove()
    } finally {
        btn_loader(btn, false);
    }
};
