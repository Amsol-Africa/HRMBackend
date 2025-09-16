import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeaveEntitlementsService from "/js/client/LeaveEntitlementsService.js";

const requestClient = new RequestClient();
const leaveEntitlementsService = new LeaveEntitlementsService(requestClient);

window.getLeaveEntitlements = async function (page = 1, leave_period = null) {
    try {
        const data = { page, leave_period_slug: leave_period };
        const leaveEntitlements = await leaveEntitlementsService.fetch(data);

        $('#leaveEntitlementsContainer').html(leaveEntitlements);

        if ($.fn.dataTable) {
            if ($.fn.dataTable.isDataTable('#leaveEntitlementsTable')) {
                $('#leaveEntitlementsTable').DataTable().destroy();
            }
            new DataTable('#leaveEntitlementsTable');
        }
    } catch (error) {
        console.error("Error loading leave entitlements:", error);
        Swal.fire('Error', 'Failed to load entitlements. Please try again.', 'error');
    }
};

window.saveLeaveEntitlements = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formId = "leaveEntitlementsForm";
    const formEl = document.getElementById(formId);
    const formData = new FormData(formEl);

    try {
        if (formData.has('leave_period_slug')) {
            await leaveEntitlementsService.update(formData);
        } else {
            await leaveEntitlementsService.save(formData);
        }
        await getLeaveEntitlements(1, formEl.querySelector('#leave_period_id')?.value || null);
        Swal.fire('Success', 'Leave entitlements saved successfully.', 'success');
    } catch (err) {
        console.error(err);
        Swal.fire('Error', err?.message || 'Failed to save leave entitlements.', 'error');
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
        $('#leaveEntitlementsFormContainer').html(form);
    } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Failed to load entitlement for editing.', 'error');
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
    } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Failed to load entitlements details.', 'error');
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
                await getLeaveEntitlements();
                Swal.fire('Deleted!', 'Leave entitlements deleted.', 'success');
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Failed to delete leave entitlements.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
