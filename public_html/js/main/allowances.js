import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AllowancesService from "/js/client/AllowancesService.js";

const requestClient = new RequestClient();
const allowancesService = new AllowancesService(requestClient);

window.getAllowances = async function (page = 1) {
    try {
        let data = { page: page };
        const allowances = await allowancesService.fetch(data);
        $("#allowancesContainer").html(allowances);

        const exportTitle = `Allowances Report - All`;
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
            action: function () { deleteSelectedAllowances(); }
        });

        const table = new DataTable('#allowancesTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[3, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 500, 1000], [5, 10, 20, 50, 100, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#allowancesTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedAllowances = function () {
            return selectedIds;
        };
    } catch (error) {
        console.error("Error loading allowances data:", error);
    }
};
async function deleteSelectedAllowances() {
    let selectedIds = window.getSelectedAllowances();
    if (selectedIds.length === 0) {
        Swal.fire("No Selection", "Please select at least one allowance to delete.", "info");
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
                await allowancesService.delete({ ids: selectedIds });
                Swal.fire("Deleted!", "Selected allowances have been deleted.", "success");
                getAllowances(1, localStorage.getItem('allowanceStatus'));
            } catch (error) {
                console.error("Error deleting allowances:", error);
                Swal.fire("Error!", "Something went wrong while deleting allowances.", "error");
            }
        }
    });
}

function sendEmailReport() {
    const subject = encodeURIComponent("Allowances Report");
    const body = encodeURIComponent("Download the report as desired file type and attach here.");
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}
window.saveAllowance = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("allowancesForm"));

    try {
        if (formData.has('allowance_slug')) {
            await allowancesService.update(formData);
        } else {
            await allowancesService.save(formData);
        }
        getAllowances();
    } finally {
        btn_loader(btn, false);
    }
};
window.editAllowance = async function (btn) {
    btn = $(btn);

    const allowance = btn.data("allowance");
    const data = { allowance: allowance };

    try {
        const form = await allowancesService.edit(data);
        $('#allowancesFormContainer').html(form)
        $("#card-header").text("Update allowance");
        setTimeout(() => {
            $("#submitButton").html('<i class="bi bi-check-circle"></i> Update allowance');
        }, 100);
    } finally {
    }
};
window.deleteAllowance = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const allowance = btn.data("allowance");
    const data = { allowance: allowance };

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
                await allowancesService.delete(data);
                getAllowances();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
