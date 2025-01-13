import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import TourPackagesService from "/js/client/TourPackagesService.js";

const requestClient = new RequestClient();
const tourPackagesService = new TourPackagesService(requestClient);
let currentView = localStorage.getItem('tourPackagesView') || 'table';

window.switchView = function(view) {
    currentView = view;
    localStorage.setItem('tourPackagesView', view);
    getTourPackages();
};

window.getTourPackages = async function(page = 1) {
    try {
        let data = { page: page, view: currentView };
        const response = await tourPackagesService.fetch(data);

        $("#tourPackagesContainer").html(response);
        if (currentView === 'table') {
            new DataTable('#tourPackagesTable');
        }
    } catch (error) {
        console.error("Error loading tour packages:", error);
    }
};


window.saveTourPackage = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    formData.append("agency_id", $("#agency").val());
    formData.append("title", $("#title").val());
    formData.append("destination", $("#destination").val());
    formData.append("description", $("#description").val());
    formData.append("start_date", $("#start_date").val());
    formData.append("price", $("#price").val());
    formData.append("duration", $("#duration").val());
    formData.append("max_participants", $("#max_participants").val());
    formData.append("minimum_participants", $("#minimum_participants").val());
    formData.append("availability", $("#availability").val());

    // Attach images
    let images = $('#images')[0].files;
    for (let i = 0; i < images.length; i++) {
        formData.append('images[]', images[i]);
    }

    try {
        await tourPackagesService.save(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.updateTourPackage = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("tour_package_slug", $("#tour_package_slug").val());
    formData.append("agency_id", $("#agency").val());
    formData.append("title", $("#title").val());
    formData.append("destination", $("#destination").val());
    formData.append("description", $("#description").val());
    formData.append("start_date", $("#start_date").val());
    formData.append("price", $("#price").val());
    formData.append("duration", $("#duration").val());
    formData.append("max_participants", $("#max_participants").val());
    formData.append("minimum_participants", $("#minimum_participants").val());
    formData.append("availability", $("#availability").val());

    // Attach images for update
    let images = $('#images')[0].files;
    for (let i = 0; i < images.length; i++) {
        formData.append('images[]', images[i]);
    }

    try {
        await tourPackagesService.update(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteTourPackage = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const tourPackageSlug = btn.data("tour-package-slug");
    const data = { tour_package_slug: tourPackageSlug };

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
                await tourPackagesService.delete(data);
                getTourPackages();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
