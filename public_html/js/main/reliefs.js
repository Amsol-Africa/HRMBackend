import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ReliefsService from "/js/client/ReliefsService.js";

const requestClient = new RequestClient();
const reliefsService = new ReliefsService(requestClient);

window.getReliefs = async function (page = 1) {
    try {
        let data = { page: page };
        const reliefsTable = await reliefsService.fetch(data);
        $("#reliefsContainer").html(reliefsTable);

        const exportTitle = `Reliefs Report`;
        const exportButtons = ['copy', 'csv', 'excel', 'pdf', 'print'].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type}`,
            title: exportTitle,
            exportOptions: { columns: ':not(:last-child)' }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () { sendEmailReport("Reliefs", window.getSelectedIds); }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () { deleteSelectedRecords("Relief", window.getSelectedIds, getReliefs); }
        });

        const table = new DataTable('#reliefsTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[0, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100, 200, 500, 1000], [5, 10, 20, 50, 100, 200, 500, 1000]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#reliefsTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).data('id');
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
        console.error("Error loading reliefs data:", error);
    }
};

async function deleteSelectedRecords(type, getSelectedIds, refreshFunction) {
    let selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire("No Selection", `Please select at least one ${type.toLowerCase()} to delete.`, "info");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: `Selected ${type.toLowerCase()}s will be permanently deleted!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: `Yes, delete selected ${type.toLowerCase()}s!`,
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await reliefsService.delete({ ids: selectedIds });
                Swal.fire("Deleted!", `Selected ${type.toLowerCase()}s have been removed.`, "success");
                refreshFunction(1);
            } catch (error) {
                console.error(`Error deleting ${type.toLowerCase()}s:`, error);
                Swal.fire("Error!", `Something went wrong while deleting ${type.toLowerCase()}s.`, "error");
            }
        }
    });
}

function sendEmailReport(reportType, getSelectedIds) {
    let selectedIds = getSelectedIds();
    let selectionText = selectedIds.length > 0 ? "for selected records" : "for all records";
    const subject = encodeURIComponent(`${reportType} Report`);
    const body = encodeURIComponent(`Hello,\n\nPlease find attached the ${reportType} report ${selectionText}.\n\nBest regards,\n[Your Company Name]`);

    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

window.showRelief = async function (btn) {
    try {
        btn = $(btn);

        $('#payrollFormulaModal').modal('show')

        const payroll_formula = btn.data("payroll-formula");
        const data = { payroll_formula: payroll_formula };

        try {
            const formulaDetails = await reliefsService.show(data);
            $('#payrollFormulaDetails').html(formulaDetails)
            $('#payrollFormulaModal').modal('show')
        } finally {
            btn_loader(btn, false);
        }

    } catch (error) {
        console.error("Error loading formula data:", error);
    }
};

window.saveRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("reliefsForm"));

    try {
        if (formData.has('relief_slug')) {
            await reliefsService.update(formData);
        } else {
            await reliefsService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const relief_slug = btn.data("relief");
    const data = { relief_slug: relief_slug };

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
                await reliefsService.delete(data);
                getReliefs();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
