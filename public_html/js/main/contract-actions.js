import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

window.getContractActions = async function (page = 1) {
    try {
        let data = { page: page };
        const response = await requestClient.post('/contracts/fetch', data);
        $("#contractActionsContainer").html(response.html);
        $("#contractActionCount").text(response.count);
    } catch (error) {
        console.error("Error loading contract actions:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load contract actions.', 'error');
    }
};

window.saveContractAction = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = $('#contractActionForm');
    let formData = new FormData(document.getElementById("contractActionForm"));

    try {
        if (formData.has("contract_action_id")) {
            await requestClient.post(`/contracts/${formData.get("contract_action_id")}/update`, formData);
            Swal.fire('Success!', 'Contract action updated successfully.', 'success');
        } else {
            await requestClient.post('/contracts/store', formData);
            Swal.fire('Success!', 'Contract action recorded successfully.', 'success');
        }
        form[0].reset();
        $('#contractActionFormContainer').html(await requestClient.post('/contracts/edit', {}));
        getContractActions();
    } catch (error) {
        console.error("Error saving contract action:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to save contract action.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.batchTerminate = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = $('#batchTerminationForm');
    let formData = new FormData(document.getElementById("batchTerminationForm"));

    if (!$('input[name="employee_ids[]"]:checked').length) {
        Swal.fire('Warning!', 'Please select at least one employee to terminate.', 'warning');
        btn_loader(btn, false);
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "This will terminate the selected employees and send termination letters.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, terminate!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post('/contracts/store', formData);
                Swal.fire('Success!', 'Employees terminated successfully.', 'success');
                form[0].reset();
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').html('');
                getContractActions();
                // Fully reload the page to refresh both contract actions and employee list
                window.location.reload();
            } catch (error) {
                console.error("Error during batch termination:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to terminate employees.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.editContractAction = async function (btn) {
    btn = $(btn);
    const contractAction = btn.data("contract-action");
    const data = { contract_action_id: contractAction };

    try {
        const response = await requestClient.post('/contracts/edit', data);
        $('#contractActionFormContainer').html(response.data);
    } catch (error) {
        console.error("Error editing contract action:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deleteContractAction = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const contractAction = btn.data("contract-action");
    const data = { contract_action_id: contractAction };

    Swal.fire({
        title: "Are you sure?",
        text: "This will reverse the termination and reactivate the employee!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post(`/contracts/${contractAction}/destroy`, data);
                Swal.fire('Deleted!', 'Contract action deleted successfully.', 'success');
                getContractActions();
            } catch (error) {
                console.error("Error deleting contract action:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete contract action.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.sendReminder = async function (employeeId) {
    Swal.fire({
        title: "Send Reminder?",
        text: "This will send a contract expiry reminder email to the employee.",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, send it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post('/contracts/remind', { employee_id: employeeId });
                Swal.fire('Sent!', 'Reminder email sent successfully.', 'success');
                getContractActions();
            } catch (error) {
                console.error("Error sending reminder:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to send reminder.', 'error');
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getContractActions();
});