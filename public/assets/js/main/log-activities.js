import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ActivitiesService from "/js/client/ActivitiesService.js";

const requestClient = new RequestClient();
const activitiesService = new ActivitiesService(requestClient);

window.logActivities = async function (btn) {
    const data = {};
    try {
        const logs = await activitiesService.fetch(data);
        $('#activityLogsContainer').html(logs)
    } finally {
    }
};
