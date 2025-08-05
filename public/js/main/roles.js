import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import RoleService from "/js/client/RoleService.js";

const requestClient = new RequestClient();
const roleService = new RoleService(requestClient);

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('roleFilter');
    const rolesContainer = document.getElementById('rolesContainer');
    const assignRoleForm = document.getElementById('assignRoleForm');

    if (rolesContainer) {
        getRoles();
    }

    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getRoles(1, filterInput.value);
        }, 300));
    }

    if (assignRoleForm) {
        assignRoleForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!assignRoleForm.checkValidity()) {
                e.stopPropagation();
                assignRoleForm.classList.add('was-validated');
                return;
            }
            assignRole(document.querySelector('button[onclick^="assignRole"]'));
        });
    }

    if ($('#assignedUsersTable').length) {
        $('#assignedUsersTable').DataTable({
            responsive: true,
            order: [[1, 'asc']],
            columnDefs: [{ targets: '_all', searchable: true }],
            language: {
                emptyTable: "No users assigned to this role",
                loadingRecords: "Loading..."
            }
        });
    }
});

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

window.getRoles = async function (page = 1, filter = '') {
    const container = $("#rolesContainer");
    if (!$.fn.DataTable) {
        container.html('<div class="alert alert-danger">DataTables library is not loaded</div>');
        toastr.error('DataTables library is not loaded', 'Error');
        return;
    }
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading roles...</div>');
        const data = { page, filter };
        const response = await roleService.fetch(data);
        console.log('Response from server:', response); // Debug log
        if (typeof response === 'string') {
            container.html(response);
        } else if (response && response.data) {
            container.html(response.data);
        } else {
            throw new Error('Invalid response format from server');
        }

        if ($('#rolesTable').length) {
            if ($.fn.DataTable.isDataTable('#rolesTable')) {
                $('#rolesTable').DataTable().destroy();
            }
            $('#rolesTable').DataTable({
                responsive: true,
                order: [[2, 'desc']],
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: "No roles available",
                    loadingRecords: "Loading..."
                }
            });
        }
    } catch (error) {
        console.error("Error loading roles:", error);
        container.html(`<div class="alert alert-danger">Error loading roles: ${error.message}</div>`);
        toastr.error('Failed to load roles: ' + error.message, "Error");
    }
};

// window.getRoles = async function (page = 1, filter = '') {
//     const container = $("#rolesContainer");
//     try {
//         container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading roles...</div>');
//         const data = { page, filter };
//         const response = await roleService.fetch(data);
//         if (typeof response === 'string') {
//             container.html(response);
//         } else if (response && response.data) {
//             container.html(response.data);
//         } else {
//             throw new Error('Invalid response format from server');
//         }

//         if ($('#rolesTable').length) {
//             console.log('DataTables available:', typeof $.fn.DataTable);
//             if ($.fn.DataTable.isDataTable('#rolesTable')) {
//                 $('#rolesTable').DataTable().destroy();
//             }

//             $('#rolesTable').DataTable({
//                 responsive: true,
//                 order: [[2, 'desc']],
//                 columnDefs: [{ targets: '_all', searchable: true }],
//                 language: {
//                     emptyTable: "No roles available",
//                     loadingRecords: "Loading..."
//                 }
//             });
//         }
//     } catch (error) {
//         console.error("Error loading roles:", error);
//         container.html(`<div class="alert alert-danger">Error loading roles: ${error.message}</div>`);
//         toastr.error('Failed to load roles: ' + error.message, "Error");
//     }
// };

window.assignRole = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const form = document.getElementById("assignRoleForm");
    const formData = new FormData(form);
    try {
        const response = await roleService.assign(formData);

        const userId = formData.get('user_id');
        const roleId = formData.get('role_id');
        const userName = $('#user_id option:selected').text().split(' (')[0];
        const userEmail = $('#user_id option:selected').text().match(/\(([^)]+)\)/)[1];

        const table = $('#assignedUsersTable').DataTable();
        const rowCount = table.rows().count() + 1;
        table.row.add([
            rowCount,
            userName,
            userEmail,
            `<button class="btn btn-danger btn-sm" data-user="${userId}" data-role="${roleId}" onclick="removeRole(this)">
                <i class="bi bi-trash"></i> Remove
            </button>`
        ]).draw();

        toastr.success('Role assigned successfully.', "Success");
        form.reset();
        form.classList.remove('was-validated');
    } catch (error) {
        toastr.error('Failed to assign role: ' + error.message, "Error");
    } finally {
        btn_loader(btn, false);
    }
};

window.removeRole = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const userId = btn.data("user");
    const roleId = btn.data("role");

    Swal.fire({
        title: "Are you sure?",
        text: "This will remove the role from the user!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, remove it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await roleService.assign({
                    role_id: roleId,
                    user_id: userId,
                    remove: true
                });

                const table = $('#assignedUsersTable').DataTable();
                const row = btn.closest('tr');
                table.row(row).remove().draw();

                toastr.success('Role removed successfully.', "Success");
            } catch (error) {
                toastr.error('Failed to remove role: ' + error.message, "Error");
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
