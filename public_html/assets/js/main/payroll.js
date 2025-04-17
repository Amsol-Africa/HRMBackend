import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

const fetchEmployees = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    const $tableContainer = $("#payrollTableContainer");
    const $previewContainer = $("#payrollPreviewContainer");
    const $tableLoader = $("#tableLoader");

    $tableContainer.empty();
    $previewContainer.empty();
    $tableLoader.show();

    try {
        const response = await requestClient.post('/payroll/fetch', formData);
        $tableContainer.html(response.data.html);
        $tableLoader.hide();
        $('#payrollForm').hide();
        $(".sidebar").toggleClass("minimized");

        $('#employeeTable').DataTable({
            responsive: true,
            pageLength: 10,
            searching: true,
            ordering: true,
            paging: true,
            language: { search: "Filter:" },
            drawCallback: function () {
                $('.exempted-row input[type="checkbox"]').prop('checked', true);
            }
        });

        if (response.data.warnings && Object.keys(response.data.warnings).length) {
            Swal.fire('Warnings', 'Some employees have missing data.', 'warning');
        }
    } catch (error) {
        $tableLoader.hide();
        Swal.fire('Error!', error.response?.data?.message || 'Failed to fetch employees.', 'error');
    }
};

function savePayrollSettings() {
    const year = $('#year').val();
    const month = $('#month').val();
    const employees = [];

    // Collect settings from the form (example)
    $('.employee-setting').each(function () {
        const employeeId = $(this).data('employee-id');
        const allowances = {};
        const deductions = {};
        const reliefs = {};
        const advanceRecovery = $(this).find('.advance-recovery').val() || 0;
        const loanRepayment = $(this).find('.loan-repayment').val() || 0;
        const overtime = {};

        $(this).find('.allowance-input').each(function () {
            const allowanceId = $(this).data('allowance-id');
            const amount = $(this).val() || 0;
            allowances[allowanceId] = amount;
        });

        $(this).find('.deduction-input').each(function () {
            const deductionId = $(this).data('deduction-id');
            const amount = $(this).val() || 0;
            deductions[deductionId] = amount;
        });

        $(this).find('.relief-input').each(function () {
            const reliefId = $(this).data('relief-id');
            const amount = $(this).val() || 0;
            reliefs[reliefId] = amount;
        });

        $(this).find('.overtime-input').each(function () {
            const overtimeId = $(this).data('overtime-id');
            const hours = $(this).val() || 0;
            overtime[overtimeId] = hours;
        });

        employees.push({
            employee_id: employeeId,
            allowances,
            deductions,
            reliefs,
            advance_recovery: advanceRecovery,
            loan_repayment: loanRepayment,
            overtime,
        });
    });

    $.ajax({
        url: '/payroll/save-settings',
        method: 'POST',
        data: {
            year: year,
            month: month,
            employees: employees,
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (response) {
            if (response.status === 'success') {
                alert('Settings saved successfully.');
                // Proceed to fetch employees
                fetchEmployees();
            } else {
                alert(response.message);
            }
        },
        error: function (xhr) {
            alert('Error saving settings: ' + xhr.responseJSON.message);
        },
    });
}

const previewPayroll = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    const $previewContainer = $("#payrollPreviewContainer");
    const $tableContainer = $("#payrollTableContainer");
    const $previewLoader = $("#previewLoader");

    $previewContainer.empty();
    $previewLoader.show();

    try {
        const response = await requestClient.post('/payroll/preview', formData);
        $previewContainer.html(response.data.html);
        $previewLoader.hide();
        $tableContainer.hide();

        $('#previewTable').DataTable({
            responsive: true,
            pageLength: 10,
            searching: true,
            ordering: true,
            paging: true,
            language: { search: "Filter:" }
        });
    } catch (error) {
        $previewLoader.hide();
        $tableContainer.show();
        Swal.fire('Error!', error.response?.data?.message || 'Failed to preview payroll.', 'error');
    }
};

const submitPayroll = async function () {
    const formData = new FormData(document.getElementById("payrollForm"));
    try {
        const response = await requestClient.post('/payroll/store', formData);
        Swal.fire('Success!', 'Payroll processed successfully.', 'success');
        window.location.href = response.data.redirect_url;
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to process payroll.', 'error');
    }
};

const addAdjustment = async function (data) {
    try {
        const response = await requestClient.post('/payroll/adjust', data);

        if (response.status === 200 || response.data) {
            Swal.fire('Success!', 'Adjustments saved successfully.', 'success');
            fetchEmployees();
            return response.data;
        } else {
            Swal.fire('Error!', response.message || 'Failed to add adjustment.', 'error');
            throw new Error(response.message || 'Adjustment failed');
        }
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to add adjustment.', 'error');
        throw error;
    }
};

const sendPayslips = async function (payrollId) {
    try {
        const response = await requestClient.post('/payroll/send-payslips', { payroll_id: payrollId });
        console.log('Send Payslips Response:', response);
        if (response.status === 200 || response.data) {
            Swal.fire('Success!', 'Payslips queued for sending.', 'success');
        } else {
            Swal.fire('Error!', response.message || 'Failed to send payslips.', 'error');
        }
    } catch (error) {
        console.error('Send Payslips Error:', error.response);
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