import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

// window.filterEmployees = async function (filters) {
//     try {
//         return await employeesService.filter(filters);
//     } catch (error) {
//         console.error("Error loading user data:", error);
//     }
// };


// window.getAllEmployeesList = async function () {
//     try {
//         let data = {};
//         return await employeesService.list(data);
//     } catch (error) {
//         console.error("Error loading user data:", error);
//     }
// };
window.filterEmployees = async function (filters) {
    try {
        return await employeesService.filter(filters);
    } catch (error) {
        console.error("Error loading user data:", error);
        return [];
    }
};

window.getAllEmployeesList = async function (filters = {}) {
    try {
        const response = await employeesService.filter({
            departments: filters.departments || [],
            job_categories: filters.job_categories || [],
            employment_terms: filters.employment_terms || [],
            locations: filters.locations || []
        });
        console.log('Filtered employees:', response);
        return response.employees || [];
    } catch (error) {
        console.error("Error loading employees:", error);
        return [];
    }
};
