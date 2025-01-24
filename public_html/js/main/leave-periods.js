import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeavePeriodsService from "/js/client/LeavePeriodsService.js";

const requestClient = new RequestClient();
const leavePeriodsService = new LeavePeriodsService(requestClient);

window.getLeavePeriods = async function (page = 1) {
    try {
        let data = {page:page};
        const leavePeriodss = await leavePeriodsService.fetch(data);
        $("#leavePeriodsContainer").html(leavePeriodss);
        new DataTable('#leavePeriodsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLeavePeriods = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("leavePeriodsForm"));

    try {
        if (formData.has('leave_period_slug')) {
            await leavePeriodsService.update(formData);
        } else {
            await leavePeriodsService.save(formData);
        }
        getLeavePeriods();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLeavePeriods = async function (btn) {
    btn = $(btn);

    const leave = btn.data("leave");
    const data = { leave: leave };

    try {
        const form = await leavePeriodsService.edit(data);
        $('#leavePeriodsFormContainer').html(form)
    } finally {
    }
};
window.viewLeavePeriods = async function (btn) {
    btn = $(btn);

    const leave_type = btn.data("leave-type");
    const data = { leave_type_slug: leave_type };

    try {
        const details = await leavePeriodsService.show(data);
        $('#leavePeriodsDetailsContent').html(details);
        $('#leavePeriodsDetailsModal').modal('show');
    } finally {
    }
};
window.deleteLeavePeriods = async function (btn) {
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
                await leavePeriodsService.delete(data);
                getLeavePeriods();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
