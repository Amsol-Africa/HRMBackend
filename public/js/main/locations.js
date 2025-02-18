import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LocationsService from "/js/client/LocationsService.js";

const requestClient = new RequestClient();
const locationsService = new LocationsService(requestClient);

window.getLocations = async function (page = 1) {
    try {
        let data = {page:page};
        const locationTable = await locationsService.fetch(data);
        $("#locationsContainer").html(locationTable);
        new DataTable('#locationsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLocation = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("locationsForm"));

    try {
        if (formData.has('location_slug')) {
            await locationsService.update(formData);
        } else {
            await locationsService.save(formData);
        }
        getLocations();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLocation = async function (btn) {
    btn = $(btn);

    const location = btn.data("location");
    const data = { location: location };

    try {
        const form = await locationsService.edit(data);
        $('#locationsFormContainer').html(form)
    } finally {
    }
};
window.deleteLocation = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const location = btn.data("location");
    const data = { location: location };

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
                await locationsService.delete(data);
                getLocations();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
