import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ActivitiesService from "/js/client/ActivitiesService.js";

const requestClient = new RequestClient();
const activitiesService = new ActivitiesService(requestClient);

window.logActivities = async function (btn) {
    const businessSlug = document.getElementById('active_business_slug')?.value;
    if (!businessSlug) {
        $('#activityLogsContainer').html('<p class="text-danger">Error: No active business selected.</p>');
        return;
    }

    const data = { business_slug: businessSlug };

    try {
        const logs = await activitiesService.fetch(data);
        $('#activityLogsContainer').html(logs);
    } catch (error) {
        $('#activityLogsContainer').html('<p class="text-danger">Failed to load activities. Please try again.</p>');
    }
};
