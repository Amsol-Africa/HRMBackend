import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import KPIsService from "/js/client/KPIsService.js";

const requestClient = new RequestClient();
const kpisService = new KPIsService(requestClient);

window.getKPIs = async function (page = 1) {
    try {
        let data = { page: page };
        const kpisCards = await kpisService.fetch(data);
        $("#kpisContainer").html(kpisCards);
    } catch (error) {
        console.error("Error loading kpis:", error);
    }
};

window.saveKpi = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("kpisForm"));

    try {
        if (formData.has("kpi_slug")) {
            await kpisService.update(formData);
        } else {
            await kpisService.save(formData);
        }
        getKPIs();
    } finally {
        btn_loader(btn, false);
    }
};

window.editKpi = async function (btn) {
    btn = $(btn);
    const kpi = btn.data("kpi");
    const data = { kpi_id: kpi };

    try {
        const form = await kpisService.edit(data);
        $('#kpisFormContainer').html(form);
    } finally {
    }
};

window.deleteKpi = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const kpi = btn.data("kpi");
    const data = { kpi_slug: kpi };

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
                await kpisService.delete(data);
                getKPIs();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

