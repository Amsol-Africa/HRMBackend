import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AdvancesService from "/js/client/AdvancesService.js";

const requestClient = new RequestClient();
const advancesService = new AdvancesService(requestClient);

window.getAdvances = async function (page = 1) {
    try {
        let data = {page:page};
        const advances = await advancesService.fetch(data);
        $("#advancesContainer").html(advances);
        new DataTable('#advancesTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveAdvance = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("advanceForm"));

    try {
        if (formData.has('advance_id')) {
            await advancesService.update(formData);
        } else {
            await advancesService.save(formData);
        }
        getAdvances();
    } finally {
        btn_loader(btn, false);
    }
};
window.editAdvance = async function (btn) {
    btn = $(btn);

    const advance = btn.data("advance");
    const data = { advance: advance };

    try {
        const form = await advancesService.edit(data);
        $('#advancesFormContainer').html(form)
    } finally {
    }
};
window.deleteAdvance = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const advance = btn.data("advance");
    const data = { advance: advance };

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
                await advancesService.delete(data);
                getAdvances();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
