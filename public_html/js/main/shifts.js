import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ShiftsService from "/js/client/ShiftsService.js";

const requestClient = new RequestClient();
const shiftsService = new ShiftsService(requestClient);

window.getShifts = async function (page = 1) {
    try {
        let data = { page: page };
        const shiftsTable = await shiftsService.fetch(data);
        $("#shiftsContainer").html(shiftsTable);

        const exportTitle = "Shifts Report";
        const exportButtons = [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ].map(type => ({
            extend: type,
            text: `<i class="fa fa-${type === 'copy' ? 'copy' : type}"></i> ${type.charAt(0).toUpperCase() + type.slice(1)}`,
            className: `btn btn-${type === 'copy' ? 'primary' : type === 'csv' ? 'secondary' : type === 'excel' ? 'success' : type === 'pdf' ? 'danger' : 'info'}`,
            title: exportTitle,
            exportOptions: {
                columns: ':not(:last-child)'
            }
        }));

        exportButtons.push({
            text: '<i class="fa fa-envelope"></i> Email',
            className: 'btn btn-warning',
            action: function () {
                sendEmailReport();
            }
        });

        exportButtons.push({
            text: '<i class="fa fa-trash"></i> Delete Selected',
            className: 'btn btn-danger',
            action: function () {
                deleteSelectedShifts();
            }
        });

        const table = new DataTable('#shiftsTable', {
            dom: 'Bfrtip',
            buttons: exportButtons
        });

        // Handle row selection
        table.on('click', 'tbody tr', function (e) {
            e.currentTarget.classList.toggle('selected');
        });

        window.getSelectedShifts = function () {
            return table.rows('.selected').data().map(row => row.id).toArray();
        };

    } catch (error) {
        console.error("Error loading shift data:", error);
    }
};

window.saveShift = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = document.getElementById("shiftsForm");
    let formData = new FormData(form);

    try {
        if (formData.has('shift_slug') && formData.get('shift_slug')) {
            await shiftsService.update(formData);

            form.reset();
            $("#shift_slug").val("");
            $("#shift_name").val("");
            $("#description").val("");
            $("#start_time").val("");
            $("#end_time").val("");

            $("#card-header").text("Add New Shift");
            setTimeout(() => {
                $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Shift');
            }, 100);
        } else {
            await shiftsService.save(formData);
        }

        getShifts();
    } finally {
        btn_loader(btn, false);
    }
};

window.editShift = async function (btn) {
    btn = $(btn);

    const shift = btn.data("shift");
    const data = { shift: shift };

    try {
        const form = await shiftsService.edit(data);
        $('#shiftsFormContainer').html(form);

        setTimeout(() => {
            $("#submitButton").html('<i class="bi bi-check-circle"></i> Update Shift');
        }, 100);

        $("#card-header").text("Edit Shift");
    } catch (error) {
        console.error("Error editing shift:", error);
    }
};

window.deleteShift = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const shift = btn.data("shift");
    const data = { shift: shift };

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
                await shiftsService.delete(data);
                getShifts();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

function sendEmailReport() {
    const subject = encodeURIComponent("Shifts Report");
    const body = encodeURIComponent("Here is the shifts report. Please find the attached file or download it from the system.");

    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

// Bulk delete selected shifts
window.deleteSelectedShifts = async function () {
    let selectedIds = window.getSelectedShifts();

    if (selectedIds.length === 0) {
        Swal.fire("No shifts selected!", "Please select shifts to delete.", "warning");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: `You are about to delete ${selectedIds.length} shift(s).`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete them!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await shiftsService.delete({ ids: selectedIds });
                Swal.fire("Deleted!", "The selected shifts have been deleted.", "success");
                getShifts();
            } catch (error) {
                console.error("Error deleting shifts:", error);
                Swal.fire("Error", "Failed to delete shifts.", "error");
            }
        }
    });
};
