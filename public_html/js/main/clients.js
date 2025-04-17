import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import BusinessesService from "/js/client/BusinessesService.js";

const requestClient = new RequestClient();
const businessesService = new BusinessesService(requestClient);

if (!window.currentBusinessSlug) {
    console.warn("currentBusinessSlug not defined, falling back to 'amsol'");
    window.currentBusinessSlug = 'amsol';
}

window.getClients = async function (page = 1) {
    try {
        const response = await businessesService.clients({ page });
        $("#clientsContainer").html(response);
        if ($('#clientsTable').length) {
            new DataTable('#clientsTable', {
                pageLength: 10,
                searching: true,
                ordering: true,
            });
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to load clients.', 'error');
        console.error("Error loading clients:", error);
    }
};

window.requestAccess = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData(document.getElementById("requestAccessForm"));

    try {
        const response = await businessesService.requestAccess(formData);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
        });
        $("#requestAccessForm")[0].reset();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to send request.',
        });
    } finally {
        btn_loader(btn, false);
    }
};

window.grantAccess = async function (btn, requestId) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('role', $('#role-' + requestId).val());

    try {
        const response = await businessesService.grantAccess(formData);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
        });
        window.location.reload();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to grant access.',
        });
    } finally {
        btn_loader(btn, false);
    }
};

window.impersonateBusiness = async function (businessSlug) {
    try {
        const response = await businessesService.post(`/businesses/${window.currentBusinessSlug}/clients/${businessSlug}/impersonate`, {});
        window.location.href = response.data.redirect_url;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to impersonate business.',
        });
    }
};

window.verifyBusiness = async function (btn, businessSlug) {
    btn = $(btn);
    $(`#remarksModal-${businessSlug}`).modal('show');
    window.currentBusinessSlugForAction = businessSlug;
    window.currentAction = 'verify';
};

window.deactivateBusiness = async function (btn, businessSlug) {
    btn = $(btn);
    $(`#remarksModal-${businessSlug}`).modal('show');
    window.currentBusinessSlugForAction = businessSlug;
    window.currentAction = 'deactivate';
};

window.submitRemarks = async function (businessSlug) {
    const remarks = $(`#remarks-${businessSlug}`).val();
    if (!remarks.trim()) {
        Swal.fire('Error', 'Remarks are required.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('remarks', remarks);

    const action = window.currentAction === 'verify' ? 'verify' : 'deactivate';
    const url = `/businesses/${window.currentBusinessSlug}/clients/${businessSlug}/${action}`;

    try {
        const response = await businessesService.post(url, formData);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
        });
        $(`#remarksModal-${businessSlug}`).modal('hide');
        getClients();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || `Failed to ${action} business.`,
        });
    }
};

window.assignModules = async function (btn, businessSlug) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData(document.getElementById("modulesForm-" + businessSlug));

    try {
        const response = await businessesService.post(`/businesses/${window.currentBusinessSlug}/clients/${businessSlug}/modules/assign`, formData);
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
        });
        $(`#modulesModal-${businessSlug}`).modal('hide');
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to assign modules.',
        });
    } finally {
        btn_loader(btn, false);
    }
};