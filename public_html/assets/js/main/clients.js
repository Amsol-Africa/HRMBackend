import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import BusinessesService from "/js/client/BusinessesService.js";

const requestClient = new RequestClient();
const clientsService = new BusinessesService(requestClient);

window.getClients = async function(page = 1) {
    try {
        let data = { page: page };
        const response = await clientsService.clients(data);
        $("#clientsContainer").html(response);
    } catch (error) {
        console.error("Error loading clients:", error);
    }
};

window.requestAccess = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("requestAccessForm"));

    try {
        await clientsService.requestAccess(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.grantAccess = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("grantAccessForm"));

    try {
        await clientsService.grantAccess(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.addClient = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    formData.append("name", $("#name").val());
    formData.append("company_size", $("#company_size").val());
    formData.append("industry", $("#industry").val());
    formData.append("phone", $("#phone").val());
    formData.append("country", $("#country").val());
    formData.append("code", $("#code").val());

    const logoInput = $("#logo")[0];
    if (logoInput.files && logoInput.files[0]) {
        formData.append("logo", logoInput.files[0]);
    }

    try {
        await clientsService.store(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.delete = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const businessSlug = btn.data("business-slug");
    const data = { slug: businessSlug };

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
                await clientsService.delete(data);
                getClients();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
