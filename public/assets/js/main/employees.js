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
// Ensure jQuery and Swal are available globally
const $ = window.jQuery || window.$;
const Swal = window.Swal || window.sweetAlert;

window.importEmployees = function (btn) {
    btn = $(btn);
    if (typeof btn_loader === 'undefined') {
        console.warn('btn_loader is not defined. Define it in app.js or pass as a parameter.');
        btn.prop('disabled', true); // Fallback disable
    } else {
        btn_loader(btn, true);
    }

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

    $.ajax({
        url: form.action,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        success: function (result) {
            console.log('AJAX response:', result);

            loadingDiv.hide();
            resultDiv.show();

            const successful = result.hasOwnProperty('successful') ? parseInt(result.successful, 10) : 0;
            console.log('Successful value:', successful);
            const errors = result.errors || [];
            const message = result.message || 'An unknown error occurred.';
            const hasErrors = errors.length > 0;

            successCount.text(`Successfully added ${successful} employees.`);
            errorCount.text(`Errors: ${errors.length}`);

            if (hasErrors) {
                errors.forEach(error => {
                    errorList.append(`<li>${error}</li>`);
                });
            }

            if (successful > 1 && !hasErrors) {
                console.log('Triggering success toast:', { title: 'Success!', text: message, icon: 'success' });
                Swal.fire({
                    title: 'Success!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else if (successful > 1 && hasErrors) {
                console.log('Triggering warning toast:', { title: 'Warning!', text: message, icon: 'warning' });
                Swal.fire({
                    title: 'Warning!',
                    text: message,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else if (successful === 0 && hasErrors) {
                console.log('Triggering error toast:', { title: 'Error!', text: message, icon: 'error' });
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                console.log('Triggering neutral toast:', { title: 'Notice!', text: message, icon: 'info' });
                Swal.fire({
                    title: 'Notice!',
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function (xhr, status, error) {
            loadingDiv.hide();
            resultDiv.show();
            errorCount.text('An unexpected error occurred.');
            errorList.append(`<li>${error}</li>`);
            Swal.fire({
                title: 'Error!',
                text: 'Something went wrong during import: ' + error,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        complete: function () {
            if (typeof btn_loader === 'undefined') {
                btn.prop('disabled', false);
            } else {
                btn_loader(btn, false);
            }
        }
    });
};
// Ensure previewImage is globally available
window.previewImage = function (event) {
    const input = event.target;
    const preview = document.getElementById('profile_preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
};
