import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import PayrollService from "/js/client/PayrollService.js";

const requestClient = new RequestClient();
const payrollService = new PayrollService(requestClient);

window.getPayrolls = async function (page = 1, location = null) {
    try {
        let data = {page:page, location:location};
        const payrollCards = await payrollService.fetch(data);
        $("#payrollsContainer").html(payrollCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.getPayslips = async function (page = 1, payroll = null) {
    try {
        let data = {page:page, payroll:payroll};
        const response = await payrollService.slips(data);

        let payslipsTable = response.payslipTable;
        let summary = response.summary;

        $("#payslipsContainer").html(payslipsTable);
        new DataTable('#payslipsTable');
        updateWidgets(summary);
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
        $('#payslipDetails').html(payslipData)
        $('#payslipDetailsModal').modal('show')
    } finally {
    }
};
window.printPayslip = async function () {
    var printContents = document.querySelector('.payslip-container').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
};
function updateWidgets(summary) {
    document.querySelector('.card.bg-primary p').textContent = summary.period;
    document.querySelector('.card.bg-success p').textContent = summary.pay_day;
    document.querySelector('.card.bg-info p').textContent = `${summary.employees} Employees`;
    document.querySelector('.card.bg-warning p').textContent = `KES ${summary.payroll_cost.toLocaleString()}`;
    document.querySelector('.card.bg-secondary p').textContent = `KES ${summary.net_pay.toLocaleString()}`;
    document.querySelector('.card.bg-danger p').textContent = `KES ${summary.taxes.toLocaleString()}`;
    document.querySelector('.card.bg-dark p').textContent = `KES ${summary.pre_tax_deductions.toLocaleString()}`;
    document.querySelector('.card.bg-light p').textContent = `KES ${summary.post_tax_deductions.toLocaleString()}`;
}

