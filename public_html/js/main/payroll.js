import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

const fetchEmployees = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    try {
        const response = await requestClient.post('/payroll/fetch', formData);
        $("#payrollTableContainer").html(response.data.html);
        $("#payrollPreviewContainer").empty();
        $('#employeeTable').DataTable({
            responsive: true,
            pageLength: 10,
            searching: true,
            ordering: true,
            paging: true,
            language: { search: "Filter:" }
        });
        if (response.data.warnings && Object.keys(response.data.warnings).length) {
            Swal.fire('Warnings', 'Some employees have missing data.', 'warning');
        }
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to fetch employees.', 'error');
    }
};

const addAdjustment = async function (employeeId, type, scope, scopeId, amount, name) {
    try {
        const response = await requestClient.post('/payroll/add-adjustment', {
            employee_id: employeeId,
            type,
            scope,
            scope_id: scopeId,
            amount,
            name
        });
        Swal.fire('Success!', response.message, 'success');
        fetchEmployees();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to add adjustment.', 'error');
    }
};

const previewPayroll = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    try {
        const response = await requestClient.post('/payroll/preview', formData);
        $("#payrollPreviewContainer").html(response.data.html);
        $('#previewTable').DataTable({
            responsive: true,
            pageLength: 10,
            searching: true,
            ordering: true,
            paging: true,
            language: { search: "Filter:" }
        });
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to preview payroll.', 'error');
        if (error.response?.data?.warnings) {
            $("#payrollTableContainer").html(error.response.data.warningsHtml || 'Resolve warnings first.');
        }
    }
};

const submitPayroll = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    try {
        const response = await requestClient.post('/payroll/store', formData);
        window.location.href = response.data.redirect_url;

        $("#payrollTableContainer").empty();
        $("#payrollPreviewContainer").empty();

    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to process payroll.', 'error');
    }
};


const sendPayslips = async function (payrollId) {
    try {
        const response = await requestClient.post('/payroll/send-payslips', { payroll_id: payrollId }); // Updated endpoint
        Swal.fire('Success!', 'Payslips queued for sending.', 'success');
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to send payslips.', 'error');
    }
};

const closePayroll = async function (payrollId) {
    Swal.fire({
        title: "Close Month?",
        text: "This will finalize the payroll. Carry forward deductions?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, carry forward",
        cancelButtonText: "No, just close",
    }).then(async (result) => {
        try {
            const data = { payroll_id: payrollId, carry_forward: result.isConfirmed ? 1 : 0 };
            const response = await requestClient.post('/payroll/close', data);
            Swal.fire('Success!', 'Payroll closed successfully.', 'success');
            window.location.reload();
        } catch (error) {
            Swal.fire('Error!', error.response?.data?.message || 'Failed to close payroll.', 'error');
        }
    });
};

const downloadReport = async function (payrollId, type) {
    if (!type) {
        console.error('Report type is required');
        return;
    }

    const currentPath = window.location.pathname;
    const pathSegments = currentPath.split('/').filter(segment => segment);
    const businessSlug = pathSegments[1];

    if (!businessSlug) {
        console.error('Could not determine business slug from URL');
        return;
    }

    const url = `/business/${businessSlug}/payroll/download-report?payroll_id=${payrollId}&type=${type}`;
    window.location.href = url;
};

// Expose functions to global scope
window.fetchEmployees = fetchEmployees;
window.addAdjustment = addAdjustment;
window.previewPayroll = previewPayroll;
window.submitPayroll = submitPayroll;
window.sendPayslips = sendPayslips;
window.closePayroll = closePayroll;
window.downloadReport = downloadReport;