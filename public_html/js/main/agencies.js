import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AgenciesService from "/js/client/AgenciesService.js";

const requestClient = new RequestClient();
const agenciesService = new AgenciesService(requestClient);

window.getAgencies = async function (page = 1) {
    try {
        let data = {page:page};
        const agenciesCards = await agenciesService.fetch(data);
        $("#agenciesContainer").html(agenciesCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveAgency = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    // Agency Details
    formData.append("agency_name", $("#agency_name").val());
    formData.append("agent", $(".agent").val());
    formData.append("size", $("#size").val());
    formData.append("founded_in", $("#founded_in").val());
    formData.append("office", $("#office").val());
    formData.append("building", $("#building").val());
    formData.append("email", $("#email").val());
    formData.append("phone", $("#phone").val());
    formData.append("code", $("#code").val());
    formData.append("country", $("#country").val());
    formData.append("category", $("#category").val());
    const selectedTourTypes = $("#tour_types").val();
    if (selectedTourTypes && selectedTourTypes.length > 0) {
        selectedTourTypes.forEach(type => {
            formData.append("tour_types[]", type);
        });
    }
    formData.append("about", $("#about").val());

    try {
        await agenciesService.save(formData);
    } finally {
        btn_loader(btn, false);
    }
};
window.deleteAgency = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const agency = btn.data("agency");
    const data = { agency: agency };

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
                await agenciesService.delete(data);
                getAgencies();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
