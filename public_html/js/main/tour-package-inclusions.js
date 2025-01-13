import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import TourPackageInclusionsService from "/js/client/TourPackageInclusionsService.js";

const requestClient = new RequestClient();
const tourPackageInclusionsService = new TourPackageInclusionsService(requestClient);

window.getTourPackageInclusions = async function (page = 1) {
    try {
        let data = { page: page, view: currentView };
        const response = await tourPackageInclusionsService.fetch(data);

        $("#tourPackageInclusionsContainer").html(response);
        new DataTable("#tourPackageInclusionsTable");
    } catch (error) {
        console.error("Error loading tour packages:", error);
    }
};

window.saveInclusions = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData();
    formData.append("tour_package_id", $("#tour_package").val());

    inclusionContainer.querySelectorAll(".inclusion-item").forEach((item, index) => {
        const title = item.querySelector('input[name="title[]"]').value;
        const extraInfo = item.querySelector('input[name="extra_info[]"]').value;

        if (title) {
            formData.append(`inclusions[${index}][title]`, title);
            formData.append(`inclusions[${index}][extra_info]`, extraInfo || "");
        }
    });

    try {
        await tourPackageInclusionsService.save(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteTourPackageInclusion = async function (inclusionId) {
    try {
        await tourPackageInclusionsService.delete({ id: inclusionId });
    } catch (error) {
        console.error("Error deleting inclusion:", error);
    }
};
