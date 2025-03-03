import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DeductionsService from "/js/client/DeductionsService.js";

const requestClient = new RequestClient();
const deductionsService = new DeductionsService(requestClient);

window.getDeductions = async function (page = 1) {
    try {
    } catch (error) {
        console.error("Error loading employee deductions data:", error);
    }
};

window.saveDeductions = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = document.getElementById("deductionsForm");
    let formData = new FormData(form);

    try {
        await deductionsService.save(formData);
        getDeduction();
    } finally {
        btn_loader(btn, false);
    }
};
