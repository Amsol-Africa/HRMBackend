import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LoansService from "/js/client/LoansService.js";

const requestClient = new RequestClient();
const loansService = new LoansService(requestClient);

window.getLoans = async function (page = 1) {
    try {
        let data = { page: page };
        const loansCards = await loansService.fetch(data);
        $("#loansContainer").html(loansCards);

        const exportTitle = `Loans Report`;
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
            action: function () { sendEmailReport("Loans", window.getSelectedLoanIds()); }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () { deleteSelectedRecords("Loan", window.getSelectedLoanIds(), getLoans); }
        });

        const table = new DataTable('#loansTable', {
            dom: '<"top"lBf>rt<"bottom"ip>',
            order: [[0, 'desc']],
            lengthMenu: [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
            pageLength: 10,
            buttons: exportButtons
        });

        let selectedIds = [];

        $('#loansTable tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');

            const id = $(this).data('id');
            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }
        });

        window.getSelectedLoanIds = function () {
            return selectedIds;
        };

    } catch (error) {
        console.error("Error loading loans data:", error);
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
                await loansService.delete({ ids: selectedIds });
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

window.saveLoan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("loanForm"));

    try {
        if (formData.has('loan_id')) {
            await loansService.update(formData);
            $("#employee_id").val("");
            $("#amount").val("");
            $("#interest_rate").val("");
            $("#term_months").val("");
            $("#start_date").val("");
            $("#notes").val("");

            $("#card-header").text("Add New Loan");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Loan');
            }, 100);
        } else {
            await loansService.save(formData);
        }
        getLoans();
    } finally {
        btn_loader(btn, false);
    }
};

window.editLoan = async function (btn) {
    btn = $(btn);

    const loan = btn.data("loan");
    const data = { loan: loan };

    try {
        const form = await loansService.edit(data);
        $('#loansFormContainer').html(form)

        setTimeout(() => {
            $("#submitButton").html('<i class="bi bi-check-circle"></i> Update Loan');
        }, 100);

        $("#card-header").text("Edit Loan");
    } finally {
    }
};

window.deleteLoan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const loan_id = btn.data("loan");
    const data = { loan_id: loan_id };

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
                await loansService.delete(data);
                getLoans();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};