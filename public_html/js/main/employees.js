import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeesService from "/js/client/EmployeesService.js";

const requestClient = new RequestClient();
const employeesService = new EmployeesService(requestClient);

window.getEmployees = async function (page = 1, status = null) {
    try {
        let data = { page: page, status: status };
        const employeesTable = await employeesService.fetch(data);

        // Ensure the right tab container gets updated
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

function sendEmailReport() {
    const subject = encodeURIComponent("Employees Report");
    const body = encodeURIComponent("Here is the employees report.");
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}


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
