import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import BidsService from "/js/client/BidsService.js";

const requestClient = new RequestClient();
const bidsService = new BidsService(requestClient);

window.getBids = async function (page = 1, agent = null) {
    try {
        let data = { page: page, agent: agent  };
        const bidsTable = await bidsService.fetch(data);
        $("#bidsContainer").html(bidsTable);
    } catch (error) {
        console.error("Error loading bids:", error);
    }
};

window.saveBid = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("agency_id", $("#agency_id").val());
    formData.append("custom_request_id", $("#custom_request_id").val());
    formData.append("amount", $("#amount").val());
    formData.append("description", $("#description").val());

    try {
        await bidsService.save(formData);
        getBids();
    } finally {
        btn_loader(btn, false);
    }
};
