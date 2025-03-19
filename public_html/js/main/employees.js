import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

// Fetch employees and initialize DataTable
window.getEmployees = async function (page = 1, status = null) {
    try {
        let data = { page: page, status: status };
        const employeesTable = await employeesService.fetch(data);

        const containerId = `#${status}Employees`;
        $(containerId).html(employeesTable);

        const exportTitle = `Employees Report - ${status.charAt(0).toUpperCase() + status.slice(1)}`;
        const exportButtons = [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type}`,
            title: exportTitle,
            exportOptions: { columns: ':not(:last-child)' }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () { sendEmailReport(); }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () { deleteSelectedEmployees(); }
        });

        const table = new DataTable(`${containerId} table`, {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[3, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 500, 1000], [5, 10, 20, 50, 100, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#employeesTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).find('.row-id').data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedIds = function () {
            return selectedIds;
        };

    } catch (error) {
        console.error("Error loading employees data:", error);
    }
};

// Delete selected employees
async function deleteSelectedEmployees() {
    let selectedIds = window.getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire("No Selection", "Please select at least one employee to delete.", "info");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete them!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await employeesService.delete({ ids: selectedIds });
                Swal.fire("Deleted!", "Selected employees have been deleted.", "success");
                getEmployees(1, localStorage.getItem('employeeStatus'));
            } catch (error) {
                console.error("Error deleting employees:", error);
                Swal.fire("Error!", "Something went wrong while deleting employees.", "error");
            }
        }
    });
}

// Send email report
function sendEmailReport() {
    const subject = encodeURIComponent("Employees Report");
    const body = encodeURIComponent("Here is the employees report.");
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

// Search employees
window.searchEmployees = async function () {
    let data = {
        page: 1,
        status: localStorage.getItem('employeeStatus') || 'active',
        name: $('#employeeName').val(),
        employee_no: $('#employeeNo').val(),
        department: $('#employeeDepartment').val(),
        location: $('#location').val(),
        gender: $('#employeeGender').val(),
    };
    try {
        const employeesTable = await employeesService.fetch(data);
        $("#employeesContainer").html(employeesTable);
    } catch (error) {
        console.error("Error filtering employees:", error);
    }
};

// Save employee
window.saveEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("employeesForm"));
    try {
        if (formData.has('employee_id')) {
            await employeesService.update(formData);
        } else {
            await employeesService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};

// Delete single employee
window.deleteEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const employee = btn.data("employee");
    const data = { employee: employee };

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
                await employeesService.delete(data);
                getEmployees();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

// Edit employee
window.editEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    try {
        const employeeId = btn.data("employee");
        const employee = await employeesService.fetch(employeeId);

        const form = document.getElementById("employeesForm");
        form.reset();

        Object.keys(employee).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'file') {
                    return;
                }
                input.value = employee[key];
            }
        });

        // Add employee_id to form for update operation
        const employeeIdInput = document.createElement('input');
        employeeIdInput.type = 'hidden';
        employeeIdInput.name = 'employee_id';
        employeeIdInput.value = employeeId;
        form.appendChild(employeeIdInput);

        // Show the modal if it exists
        const modal = $('#employeeModal');
        if (modal.length) {
            modal.modal('show');
        }

    } catch (error) {
        console.error("Error loading employee data:", error);
        Swal.fire("Error!", "Failed to load employee data.", "error");
    } finally {
        btn_loader(btn, false);
    }
};

// Import employees
window.importEmployees = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const form = document.getElementById('importEmployeesForm');
    const formData = new FormData(form);
    const loadingDiv = $('#loading');
    const resultDiv = $('#import-result');
    const progressText = $('#progress');
    const successCount = $('#success-count');
    const errorCount = $('#error-count');
    const errorList = $('#error-list');

    loadingDiv.show();
    resultDiv.hide();
    progressText.text('');
    successCount.text('');
    errorCount.text('');
    errorList.empty();

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        const result = await response.json();

        loadingDiv.hide();
        resultDiv.show();

        // Normalize errors array
        const errors = result.data?.errors || result.errors?.errors || [];
        const message = result.message || 'An unknown error occurred.';

        if (result.success) {
            const successful = result.data?.successful || 0;
            successCount.text(`Successfully added ${successful} employees.`);
            errorCount.text(`Errors: ${errors.length}`);

            if (errors.length > 0) {
                errors.forEach(error => {
                    errorList.append(`<li>${error}</li>`);
                });
            }

            if (successful > 0) {
                Swal.fire('Success!', message, 'success');
            } else if (errors.length === 0) {
                Swal.fire('Warning!', message, 'warning');
            } else {
                Swal.fire('Error!', message, 'error');
            }
        } else {
            errorCount.text(`Errors: ${errors.length}`);
            if (errors.length > 0) {
                errors.forEach(error => {
                    errorList.append(`<li>${error}</li>`);
                });
            }
            Swal.fire('Error!', message, 'error');
        }
    } catch (error) {
        loadingDiv.hide();
        resultDiv.show();
        errorCount.text('An unexpected error occurred.');
        errorList.append(`<li>${error.message}</li>`);
        Swal.fire('Error!', 'Something went wrong during import: ' + error.message, 'error');
    } finally {
        btn_loader(btn, false);
    }
};