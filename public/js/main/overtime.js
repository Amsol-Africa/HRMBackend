import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import OvertimeService from "/js/client/OvertimeService.js";

const requestClient = new RequestClient();
const overtimeService = new OvertimeService(requestClient);

window.getOvertime = async function (date = null) {
    try {
        let data = {date: date};
        const overtime = await overtimeService.fetch(data);
        $("#overtimeContainer").html(overtime);
        new DataTable('#overtimeTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveOvertime = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("overtimeForm"));

    try {
        if (formData.has('overtime_slug')) {
            await overtimeService.update(formData);
        } else {
            await overtimeService.save(formData);
        }
    } finally {
        $('#addOvertimeModal').hide()
        getOvertime();
        btn_loader(btn, false);
    }
};
window.editOvertime = async function (btn) {
    btn = $(btn);

    const overtime = btn.data("overtime");
    const data = { overtime: overtime };

    try {
        const form = await overtimeService.edit(data);
        $('#overtimesFormContainer').html(form)
    } finally {
    }
};
window.deleteOvertime = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const overtime = btn.data("overtime");
    const data = { overtime: overtime };

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
                await overtimeService.delete(data);
            } finally {
                getOvertime();
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
