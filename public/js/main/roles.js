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
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading roles...</div>');
        const data = { page, filter };
        const response = await roleService.fetch(data);
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

window.saveRole = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const formData = new FormData(document.getElementById("roleForm"));
    try {
        if (formData.has('role_name')) {
            await roleService.update(formData);
        } else {
            await roleService.save(formData);
        }
        setTimeout(() => {
            window.location.href = '/roles';
        }, 1500);
    } catch (error) {
        toastr.error('Failed to save role: ' + error.message, "Error");
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteRole = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const role = btn.data("role");

    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await roleService.delete({ role });
                await getRoles();
            } catch (error) {
                toastr.error('Failed to delete role: ' + error.message, "Error");
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.assignRole = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const formData = new FormData(document.getElementById("assignRoleForm"));
    try {
        await roleService.assign(formData);
        toastr.success('Role assigned successfully.', "Success");
        setTimeout(() => {
            window.location.reload();
        }, 1000);
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
                const response = await requestClient.post('/roles/assign', {
                    role_id: roleId,
                    user_id: userId,
                    _method: 'DELETE'
                });
                toastr.success('Role removed successfully.', "Success");
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
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