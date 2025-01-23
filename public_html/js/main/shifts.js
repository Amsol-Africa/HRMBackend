import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ShiftsService from "/js/client/ShiftsService.js";

const requestClient = new RequestClient();
const shiftsService = new ShiftsService(requestClient);

window.getShifts = async function (page = 1) {
    try {
        let data = {page:page};
        const shiftsCards = await shiftsService.fetch(data);
        $("#shiftsContainer").html(shiftsCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveShift = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("shiftsForm"));

    try {
        if (formData.has('shift_slug')) {
            await shiftsService.update(formData);
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
        $('#shiftsFormContainer').html(form)
    } finally {
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
