import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);
let dataTable;

document.addEventListener('DOMContentLoaded', () => {
    initializeDataTable();
    setupFilters();
});

function initializeDataTable() {
    dataTable = $('#employeesTable').DataTable({
        responsive: true,
        pageLength: 10,
        searching: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: '/employees/fetch',
            type: 'POST',
            data: function (d) {
                d.search = $('#search').val();
                d.department = $('#filterDepartment').val();
                d.location = $('#filterLocation').val();
                d.job_category = $('#filterJobCategory').val();
            },
            beforeSend: function () {
                $('#loadingRow').show();
            },
            complete: function () {
                $('#loadingRow').hide();
            },
            error: function (xhr, error, thrown) {
                $('#loadingRow').hide();
                console.error('DataTables error:', xhr.responseText);
                Swal.fire('Error!', 'Failed to load table data.', 'error');
            }
        },
        columns: [
            { data: 'name' },
            { data: 'employee_code' },
            { data: 'department' },
            { data: 'job_category' },
            { data: 'location' },
            { data: 'basic_salary' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });
}

function setupFilters() {
    $('#search, #filterDepartment, #filterLocation, #filterJobCategory').on('change keyup', debounce(() => {
        dataTable.ajax.reload();
    }, 300));
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

window.createEmployee = async function () {
    try {
        const response = await employeesService.edit({});
        if (response) {
            $('#employeeFormContainer').html(response);
            $('#employeeModalLabel').text('Add Employee');
            $('#employeeModal').modal('show');
        } else {
            throw new Error('No data returned from server');
        }
    } catch (error) {
        console.error('Create Employee Error:', error.response || error);
        // Swal.fire('Error!', error.response?.data?.message || 'Failed to load create form.', 'error');
    }
};

window.saveEmployee = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const formData = new FormData(document.getElementById('employeeForm'));

    try {
        if (formData.has('employee_id')) {
            await employeesService.update(formData);
        } else {
            await employeesService.save(formData);
        }
        $('#employeeModal').modal('hide');
        dataTable.ajax.reload();
    } catch (error) {
        console.log('Save Employee Error', error.response || error);
        // Swal.fire('Error!', error.response?.data?.error || 'Failed to save employee.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editEmployee = async function (id) {
    try {
        $('#employeeFormContainer').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        const response = await employeesService.edit({ employee_id: id });
        if (response) {
            $('#employeeFormContainer').html(response);
            $('#employeeModalLabel').text('Edit Employee');
            $('#employeeModal').modal('show');
        } else {
            throw new Error('No data returned from server');
        }
    } catch (error) {
        console.error('Edit Employee Error:', error.response || error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deleteEmployee = async function (id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await employeesService.delete({ employee_id: id });
                dataTable.ajax.reload();
            } catch (error) {
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete employee.', 'error');
            }
        }
    });
};

window.viewEmployee = async function (id) {
    try {
        $('#viewLoading').show();
        $('#employeeTabContent').hide();
        const response = await employeesService.view({ employee_id: id });
        if (response) {
            $('#viewEmployeeContainer').html(response);
            $('#viewLoading').hide();
            $('#employeeTabContent').show();
            $('#viewEmployeeModal').modal('show');
        } else {
            throw new Error('No data returned from server');
        }
    } catch (error) {
        $('#viewLoading').hide();
        console.error('View Employee Error:', error.response || error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load employee details.', 'error');
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