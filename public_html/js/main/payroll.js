import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import PayrollService from "/js/client/PayrollService.js";

const requestClient = new RequestClient();
const payrollService = new PayrollService(requestClient);

window.getPayrolls = async function (page = 1) {
    try {
        let data = {page:page};
        const payrollCards = await payrollService.fetch(data);
        $("#payrollsContainer").html(payrollCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.getPayslips = async function (page = 1, payroll = null) {
    try {
        let data = {page:page, payroll:payroll};
        const payslipsTable = await payrollService.slips(data);
        $("#payslipsContainer").html(payslipsTable);
        new DataTable('#payslipsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.processPayroll = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("processPayroll"));

    try {
        $payroll_id = await payrollService.save(formData);
        getPayslips(1, payroll_id);
    } finally {
        btn_loader(btn, false);
    }
};
window.editPayroll = async function (btn) {
    btn = $(btn);

    const payroll = btn.data("payroll");
    const data = { payroll: payroll };

    try {
        const form = await payrollService.edit(data);
        $('#payrollsFormContainer').html(form)
    } finally {
    }
};
window.deletePayroll = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const payroll = btn.data("payroll");
    const data = { payroll: payroll };

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
                await payrollService.delete(data);
                getPayrolls();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
window.viewPayslipDetails = async function (btn) {
    btn = $(btn);

    const payslip = btn.data("payslip");
    const data = { payslip: payslip };

    try {
        const payslipData = await payrollService.viewPayslip(data);
        $('#payslipContainer').html(payslipData)
        $('#payslipModal').modal('show')
    } finally {
    }
};
