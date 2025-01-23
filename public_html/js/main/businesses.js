import { btn_loader } from "https://amsol.anzar.co.ke/public_html/js/client/config.js";
import RequestClient from "https://amsol.anzar.co.ke/public_html/js/client/RequestClient.js";
import BusinessesService from "https://amsol.anzar.co.ke/public_html/js/client/BusinessesService.js";

const requestClient = new RequestClient();
const businessesService = new BusinessesService(requestClient);
let currentView = localStorage.getItem('tourPackagesView') || 'table';

window.switchView = function(view) {
    currentView = view;
    localStorage.setItem('businessesView', view);
    getBusinesses();
};

window.getBusinesses = async function(page = 1) {
    try {
        let data = { page: page, view: currentView };
        const response = await businessesService.fetch(data);

        $("#businessesContainer").html(response);
        if (currentView === 'table') {
            new DataTable('#businessesTable');
        }
    } catch (error) {
        console.error("Error loading businesses:", error);
    }
};

window.register = async function (btn) {
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
        await businessesService.store(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.saveModules = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    let formData = new FormData(document.getElementById("modulesForm"));
    try {
        await businessesService.saveModules(formData);
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
                await businessesService.delete(data);
                getBusinesses();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
