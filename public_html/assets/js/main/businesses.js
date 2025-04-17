import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import BusinessesService from "/js/client/BusinessesService.js";

const requestClient = new RequestClient();
const businessesService = new BusinessesService(requestClient);
let currentView = localStorage.getItem('tourPackagesView') || 'table';

window.switchView = function (view) {
    currentView = view;
    localStorage.setItem('businessesView', view);
    getBusinesses();
};

window.getBusinesses = async function (page = 1) {
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

    const form = document.getElementById("hrmSetupForm");
    if (!form) {
        console.error("Form #hrmSetupForm not found.");
        btn_loader(btn, false);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Form not found. Please try again.",
            confirmButtonText: "OK",
        });
        return;
    }

    const formData = new FormData(form);
    const logoInput = document.getElementById("logo");

    // Validate logo is provided
    if (!logoInput?.files?.length) {
        btn_loader(btn, false);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Please upload a logo.",
            confirmButtonText: "OK",
        });
        return;
    }

    formData.append("logo", logoInput.files[0]);

    // Log FormData for debugging
    console.log("FormData contents:", Array.from(formData.entries()));

    try {
        // Verify the endpoint URL
        const endpoint = '/businesses/store';
        console.log("Sending request to:", endpoint);

        // Add timeout to prevent hanging
        const response = await Promise.race([
            businessesService.store(formData),
            new Promise((_, reject) => setTimeout(() => reject(new Error("Request timed out after 30 seconds")), 30000))
        ]);

        console.log("Response received:", response);

        if (response.redirect_url) {
            await Swal.fire({
                icon: "success",
                title: "Success",
                text: "Business registered successfully!",
                confirmButtonText: "OK",
            });
            window.location.href = response.redirect_url;
        } else {
            throw new Error("Redirect URL not provided in response.");
        }
    } catch (error) {
        console.error("Error registering business:", {
            message: error.message,
            response: error.response,
            stack: error.stack
        });
        let errorMessage = error.response?.data?.message || error.message || "Failed to register business.";
        if (error.response?.data?.errors) {
            errorMessage = Object.values(error.response.data.errors).flat().join("<br>");
        }
        await Swal.fire({
            icon: "error",
            title: "Error",
            html: errorMessage,
            confirmButtonText: "OK",
        });
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

window.updateBusiness = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("businessDetailsForm"));

    try {
        await businessesService.update(formData);
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