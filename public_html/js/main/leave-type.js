import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeaveTypeService from "/js/client/LeaveTypeService.js";

const requestClient = new RequestClient();
const leaveTypeService = new LeaveTypeService(requestClient);

window.getLeaveType = async function (page = 1, status = 'pending') {
    try {
        let data = {page:page, status: status};
        const leaveCards = await leaveTypeService.fetch(data);
        $("#leaveTypeContainer").html(leaveCards);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLeaveType = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("leaveForm"));

    try {
        if (formData.has('leave_slug')) {
            await leaveTypeService.update(formData);
        } else {
            await leaveTypeService.save(formData);
        }
        getLeaveType();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLeaveType = async function (btn) {
    btn = $(btn);

    const leave = btn.data("leave");
    const data = { leave: leave };

    try {
        const form = await leaveTypeService.edit(data);
        $('#leaveTypeFormContainer').html(form)
    } finally {
    }
};
window.deleteLeaveType = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const leave_type = btn.data("leave-type");
    const data = { leave_type: leave_type };

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
                await leaveTypeService.delete(data);
                getLeaveType();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
