import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import PayrollService from "/js/client/PayrollService.js";

const requestClient = new RequestClient();
const payrollService = new PayrollService(requestClient);

window.getPayrolls = async function (page = 1, location = null) {
    try {
        let data = { page: page, location: location };
        const payrollTable = await payrollService.fetch(data);
        $("#payrollsContainer").html(payrollTable);

        const exportTitle = `Payrolls Report - ${location ? location : 'All'}`;
        const exportButtons = ['copy', 'csv', 'excel', 'pdf', 'print'].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type}`,
            title: exportTitle,
            exportOptions: { columns: ':not(:last-child)' }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () { sendEmailReport("Payrolls", window.getSelectedPayrollIds()); }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () { deleteSelectedRecords("Payroll", window.getSelectedPayrollIds(), getPayrolls); }
        });

        const table = new DataTable('#payrollTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[1, 'desc'], [0, 'desc']], // Sort by year then month
            lengthMenu: [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#payrollTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedPayrollIds = function () {
            return selectedIds;
        };

    } catch (error) {
        console.error("Error loading payroll data:", error);
    }
};


window.getPayslips = async function (page = 1, payroll = null) {
    try {
        let data = { page: page, payroll: payroll };
        const response = await payrollService.slips(data);

        let payslipsTable = response.payslipTable;
        let summary = response.summary;

        $("#payslipsContainer").html(payslipsTable);

        const exportTitle = `Payslips Report - ${payroll ? payroll : 'All'}`;
        const exportButtons = ['copy', 'csv', 'excel', 'pdf', 'print'].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type}`,
            title: exportTitle,
            exportOptions: { columns: ':not(:last-child)' }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () { sendEmailReport("Payslips", window.getSelectedPayslipIds()); }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () { deleteSelectedRecords("Payslip", window.getSelectedPayslipIds(), getPayslips); }
        });

        const table = new DataTable('#payslipsTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[3, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 500, 1000], [5, 10, 20, 50, 100, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#payslipsTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).find('.row-id').data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedPayslipIds = function () {
            return selectedIds;
        };

        updateWidgets(summary);
    } catch (error) {
        console.error("Error loading payslips data:", error);
    }
};

async function deleteSelectedRecords(type, getSelectedIds, refreshFunction) {
    let selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire("No Selection", `Please select at least one ${type.toLowerCase()} to delete.`, "info");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: `Selected ${type.toLowerCase()}s will be permanently deleted!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: `Yes, delete selected ${type.toLowerCase()}s!`,
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await payrollService.delete({ ids: selectedIds });
                Swal.fire("Deleted!", `Selected ${type.toLowerCase()}s have been removed.`, "success");
                refreshFunction(1, localStorage.getItem('payrollStatus'));
            } catch (error) {
                console.error(`Error deleting ${type.toLowerCase()}s:`, error);
                Swal.fire("Error!", `Something went wrong while deleting ${type.toLowerCase()}s.`, "error");
            }
        }
    });
}

function sendEmailReport(reportType, getSelectedIds) {
    let selectedIds = getSelectedIds();
    let selectionText = selectedIds.length > 0 ? "for selected records" : "for all records";
    const subject = encodeURIComponent(`${reportType} Report`);
    const body = encodeURIComponent(`Hello,\n\nPlease find attached the ${reportType} report ${selectionText}.\n\nBest regards,\n[Your Company Name]`);

    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

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
    document.getElementById('period').textContent = summary.period;
    document.getElementById('pay-day').textContent = summary.pay_day;
    document.getElementById('employees').textContent = `${summary.employees} Employees`;
    document.getElementById('payroll-cost').textContent = `KES ${summary.payroll_cost.toLocaleString()}`;
    document.getElementById('net-pay').textContent = `KES ${summary.net_pay.toLocaleString()}`;
    document.getElementById('taxes').textContent = `KES ${summary.taxes.toLocaleString()}`;
    document.getElementById('pre-tax-deductions').textContent = `KES ${summary.pre_tax_deductions.toLocaleString()}`;
    document.getElementById('post-tax-deductions').textContent = `KES ${summary.post_tax_deductions.toLocaleString()}`;
}


