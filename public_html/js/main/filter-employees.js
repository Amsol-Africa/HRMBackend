import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

window.filterEmployees = async function (filters) {
    try {
        return await employeesService.filter(filters);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.getAllEmployeesList = async function () {
    try {
        let data = {};
        return await employeesService.list(data);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};