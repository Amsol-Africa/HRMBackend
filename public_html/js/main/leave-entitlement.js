import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeaveEntitlementsService from "/js/client/LeaveEntitlementsService.js";

const requestClient = new RequestClient();
const leaveEntitlementsService = new LeaveEntitlementsService(requestClient);

window.getLeaveEntitlements = async function (page = 1, location = null) {
    try {
        let data = { page: page, location: location };
        const leaveEntitlementss = await leaveEntitlementsService.fetch(data);
        $('#leaveEntitlementsContainer').html(leaveTable);
        new DataTable('#leaveEntitlementsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLeaveEntitlements = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("leaveEntitlementsForm"));

    try {
        if (formData.has('leave_period_slug')) {
            await leaveEntitlementsService.update(formData);
        } else {
            await leaveEntitlementsService.save(formData);
        }
        getLeaveEntitlements();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLeaveEntitlements = async function (btn) {
    btn = $(btn);

    const leave = btn.data("leave");
    const data = { leave: leave };

    try {
        const form = await leaveEntitlementsService.edit(data);
        $('#leaveEntitlementsFormContainer').html(form)
    } finally {
    }
};
window.viewLeaveEntitlements = async function (btn) {
    btn = $(btn);

    const leave_type = btn.data("leave-type");
    const data = { leave_type_slug: leave_type };

    try {
        const details = await leaveEntitlementsService.show(data);
        $('#leaveEntitlementsDetailsContent').html(details);
        $('#leaveEntitlementsDetailsModal').modal('show');
    } finally {
    }
};
window.deleteLeaveEntitlements = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const leave_type = btn.data("leave-type");
    const data = { leave_type_slug: leave_type };

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
                await leaveEntitlementsService.delete(data);
                getLeaveEntitlements();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
