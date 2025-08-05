import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();
let selectedPayrolls = [];

const toggleSelectAll = function () {
    const selectAll = document.getElementById('selectAllPayrolls');
    const checkboxes = document.querySelectorAll('.payrollCheckbox');
    checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
    updateSelectedPayrolls();
};

const updateSelectedPayrolls = function () {
    selectedPayrolls = Array.from(document.querySelectorAll('.payrollCheckbox:checked')).map(checkbox => checkbox.value);
};

const filterPayrolls = async function () {
    const formData = new FormData(document.getElementById("payrollFilterForm"));
    try {
        const response = await requestClient.post('/payroll/filter', formData);
        document.getElementById("pastPayrollsContainer").innerHTML = response.data.html;
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to filter payrolls.', 'error');
    }
};

const clearFilters = function () {
    document.getElementById("payrollFilterForm").reset();
    filterPayrolls();
};

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
                    const response = await requestClient.post(`/payroll/${payrollId}/delete`, {});
                    if (response.data) {
                        // Update summary
                        document.querySelector('.text-danger').textContent = `${response.data.payroll_count} payroll(s) found`;
                        document.querySelector('h5.text-muted').innerHTML = `
                            <span class="text-danger">${response.data.payroll_count} payroll(s) found</span> |
                            Total Payroll: ${response.data.total_payroll} |
                            Total Net Pay: ${response.data.total_net_pay}
                        `;
                    }
                }
                Swal.fire('Deleted!', 'Payroll(s) deleted successfully.', 'success');
                filterPayrolls();
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete payroll.', 'error');
            }
        }
    });
};

const closeMonth = async function (id = null) {
    if (!id && selectedPayrolls.length === 0) {
        Swal.fire('Error!', 'Please select at least one payroll to close/open.', 'error');
        return;
    }

    const payrollIds = id ? [id] : selectedPayrolls;
    try {
        for (const payrollId of payrollIds) {
            const response = await requestClient.post(`/payroll/${payrollId}/close`, {});
            const row = document.querySelector(`.payrollCheckbox[value="${payrollId}"]`).closest('tr');
            const statusCell = row.querySelector('td:nth-child(4)');
            statusCell.textContent = response.data.status === 'closed' ? 'closed' : 'open';
        }
        Swal.fire('Success!', payrollIds.length > 1 ? 'Payroll months updated successfully.' : 'Payroll month updated successfully.', 'success');
        filterPayrolls();
    } catch (error) {
        Swal.fire('Error!', error.response?.data?.message || 'Failed to update payroll status.', 'error');
    }
};

const emailPayslips = async function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to send payslips for.', 'error');
        return;
    }

    const payrollId = id || selectedPayrolls[0];
    const payrollRow = document.querySelector(`.payrollCheckbox[value="${payrollId}"]`).closest('tr');
    const payrollMonth = payrollRow.querySelector('td:nth-child(2)').textContent.trim();
    const businessSlug = window.businessSlug; // Ensure this is available in the Blade view

    const result = await Swal.fire({
        title: "Are you sure?",
        text: `You are about to send payslips for ${payrollMonth}. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, send payslips!",
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/business/${businessSlug}/payroll/send-payslips`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payroll_id: payrollId
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Server Error (${response.status}): ${errorText}`);
        }

        const data = await response.json();
        Swal.fire('Success!', data.message || `Payslips for ${payrollMonth} have been queued for sending.`, 'success');
        payrollRow.querySelector('td:nth-child(5)').textContent = '✔';
    } catch (error) {
        console.error('Error sending payslips:', error);
        Swal.fire('Error!', error.message || 'Failed to send payslips.', 'error');
    }
};

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

const downloadPayroll = function (id = null) {
    if (!id && selectedPayrolls.length !== 1) {
        Swal.fire('Error!', 'Please select exactly one payroll to download.', 'error');
        return;
    }

    const businessSlug = window.businessSlug ;
    const payrollId = id || selectedPayrolls[0];

    if (!businessSlug) {
        Swal.fire('Error!', 'Business slug not found. Please reload the page.', 'error');
        console.error('Business slug is undefined');
        return;
    }

    const format = 'xlsx';
    window.location.href = `/business/${businessSlug}/payroll/${payrollId}/download/${format}`;
};

const viewPayroll = function (id) {
    const currentPath = window.location.pathname;
    const pathSegments = currentPath.split('/').filter(segment => segment);
    const businessSlug = pathSegments[1];
    if (!businessSlug) {
        console.error('Could not determine business slug from URL');
        return;
    }
    window.location.href = `/business/${businessSlug}/payroll/${id}`;
};

window.toggleSelectAll = toggleSelectAll;
window.updateSelectedPayrolls = updateSelectedPayrolls;
window.filterPayrolls = filterPayrolls;
window.clearFilters = clearFilters;
window.processPayroll = processPayroll;
window.deletePayroll = deletePayroll;
window.closeMonth = closeMonth;
window.emailPayslips = emailPayslips;
window.emailP9 = emailP9;
window.downloadPayroll = downloadPayroll;
window.viewPayroll = viewPayroll;

document.addEventListener('DOMContentLoaded', () => {
    filterPayrolls();
});
