import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();
let selectedPayrolls = [];

// Toggle select all checkboxes
const toggleSelectAll = function () {
    const selectAll = document.getElementById('selectAllPayrolls');
    const checkboxes = document.querySelectorAll('.payrollCheckbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    updateSelectedPayrolls();
};

// Update the list of selected payrolls
const updateSelectedPayrolls = function () {
    selectedPayrolls = Array.from(document.querySelectorAll('.payrollCheckbox:checked')).map(checkbox => checkbox.value);
};

// Filter payrolls based on form inputs
const filterPayrolls = async function () {
    const formData = new FormData(document.getElementById("payrollFilterForm"));
    try {
        const response = await requestClient.post('/payroll/filter', formData);
        document.getElementById("pastPayrollsContainer").innerHTML = response.data.html;
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to filter payrolls.', 'error');
    }
};

// Clear filter form and refresh payrolls
const clearFilters = function () {
    document.getElementById("payrollFilterForm").reset();
    filterPayrolls();
};

// Process a payroll
const processPayroll = async function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to process.', 'error');
        return;
    }

    const payrollId = id || selectedPayrolls[0];
    try {
        const response = await requestClient.post(`/payroll/${payrollId}/process`, {});
        Swal.fire('Success!', response.message, 'success');
        filterPayrolls();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to process payroll.', 'error');
    }
};

// Delete selected payrolls
const deletePayroll = async function (id = null) {
    if (!id && selectedPayrolls.length === 0) {
        Swal.fire('Error!', 'Please select at least one payroll to delete.', 'error');
        return;
    }

    const payrollIds = id ? [id] : selectedPayrolls;
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
                for (const payrollId of payrollIds) {
                    await requestClient.post(`/payroll/${payrollId}/delete`, {});
                }
                Swal.fire('Deleted!', 'Payroll(s) deleted successfully.', 'success');
                filterPayrolls();
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete payroll.', 'error');
            }
        }
    });
};

// Publish selected payrolls
const publishPayroll = async function (id = null) {
    if (!id && selectedPayrolls.length === 0) {
        Swal.fire('Error!', 'Please select at least one payroll to publish.', 'error');
        return;
    }

    const payrollIds = id ? [id] : selectedPayrolls;
    try {
        for (const payrollId of payrollIds) {
            await requestClient.post(`/payroll/${payrollId}/publish`, {});
        }
        Swal.fire('Success!', 'Payroll(s) published successfully.', 'success');
        filterPayrolls();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to publish payroll.', 'error');
    }
};

// Unpublish selected payrolls
const unpublishPayroll = async function (id = null) {
    if (!id && selectedPayrolls.length === 0) {
        Swal.fire('Error!', 'Please select at least one payroll to unpublish.', 'error');
        return;
    }

    const payrollIds = id ? [id] : selectedPayrolls;
    try {
        for (const payrollId of payrollIds) {
            await requestClient.post(`/payroll/${payrollId}/unpublish`, {});
        }
        Swal.fire('Success!', 'Payroll(s) unpublished successfully.', 'success');
        filterPayrolls();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to unpublish payroll.', 'error');
    }
};

// Send payslips for a single payroll
const emailPayslips = async function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to send payslips for.', 'error');
        return;
    }

    const payrollId = id || selectedPayrolls[0];
    const payrollRow = document.querySelector(`.payrollCheckbox[value="${payrollId}"]`).closest('tr');
    const payrollMonth = payrollRow.querySelector('td:nth-child(2)').textContent.trim();

    // Confirmation prompt
    const result = await Swal.fire({
        title: "Are you sure?",
        text: `You are about to send payslips for ${payrollMonth}. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, send payslips!",
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await requestClient.post('/payroll/send-payslips', { payroll_id: payrollId });
        console.log('Send Payslips Response:', response);
        if (response.status === 200 || response.data) {
            Swal.fire('Success!', `Payslips for ${payrollMonth} have been queued for sending.`, 'success');
            // Update the "Emailed" column to show ✔
            const emailedCell = payrollRow.querySelector('td:nth-child(5)');
            emailedCell.textContent = '✔';
        } else {
            Swal.fire('Error!', response.message || 'Failed to send payslips.', 'error');
        }
    } catch (error) {
        console.error('Send Payslips Error:', error.response);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to send payslips.', 'error');
    }
};

// Email P9 forms for selected payrolls
const emailP9 = async function (id = null) {
    if (!id && selectedPayrolls.length === 0) {
        Swal.fire('Error!', 'Please select at least one payroll to email P9 forms.', 'error');
        return;
    }

    const payrollIds = id ? [id] : selectedPayrolls;
    try {
        for (const payrollId of payrollIds) {
            await requestClient.post(`/payroll/${payrollId}/email-p9`, {});
        }
        Swal.fire('Success!', 'P9 forms emailed successfully.', 'success');
        filterPayrolls();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to email P9 forms.', 'error');
    }
};

// Download a payroll report
const downloadPayroll = async function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to download.', 'error');
        return;
    }
    const businessSlug = '{{ $business->slug }}';
    const payrollId = id || selectedPayrolls[0];
    window.location.href = `business/${businessSlug}/payroll/${payrollId}/download`;
};

// Print all payslips for a payroll
const printAllPayslips = async function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to print payslips.', 'error');
        return;
    }

    const payrollId = id || selectedPayrolls[0];
    window.location.href = `/payroll/${payrollId}/print-all-payslips`;
};

const viewPayroll = function (id) {
    const currentPath = window.location.pathname;
    const pathSegments = currentPath.split('/').filter(segment => segment);
    const businessSlug = pathSegments[1]; // Assuming business slug is second segment

    if (!businessSlug) {
        console.error('Could not determine business slug from URL');
        return;
    }

    const url = `/business/${businessSlug}/payroll/${id}`;
    window.location.href = url;
};

// Expose functions to the global scope
window.toggleSelectAll = toggleSelectAll;
window.updateSelectedPayrolls = updateSelectedPayrolls;
window.filterPayrolls = filterPayrolls;
window.clearFilters = clearFilters;
window.processPayroll = processPayroll;
window.deletePayroll = deletePayroll;
window.publishPayroll = publishPayroll;
window.unpublishPayroll = unpublishPayroll;
window.emailPayslips = emailPayslips;
window.emailP9 = emailP9;
window.downloadPayroll = downloadPayroll;
window.printAllPayslips = printAllPayslips;
window.viewPayroll = viewPayroll;

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    filterPayrolls();
});