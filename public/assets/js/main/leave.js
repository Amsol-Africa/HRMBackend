import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeaveService from "/js/client/LeaveService.js";

const requestClient = new RequestClient();
const leaveService = new LeaveService(requestClient);

window.getLeave = async function (page = 1, status = 'pending') {
    try {
        let data = { page: page, status: status };
        const leaveTable = await leaveService.fetch(data);
        $(`#${status}Container`).html(leaveTable);
        new DataTable(`#${status}LeaveRequestsTable`);
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLeave = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    let formData = new FormData(document.getElementById("leaveForm"));
    try {
        if (formData.has('leave_slug')) {
            await leaveService.update(formData);
        } else {
            await leaveService.save(formData);
        }
        getLeave();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLeave = async function (btn) {
    btn = $(btn);

    const leave = btn.data("leave");
    const data = { leave: leave };

    try {
        const form = await leaveService.edit(data);
        $('#leaveFormContainer').html(form)
    } finally {
    }
};
window.deleteLeave = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const leave = btn.data("leave");
    const data = { leave: leave };

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
                await leaveService.delete(data);
                getLeave();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
window.manageLeave = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const leave = btn.data("leave");
    const action = btn.data("action");
    const status = action === "reject" ? "rejected" : "approved";

    let data = { reference_number: leave, status: status };

    if (action === "reject") {
        const { value: reject_reason } = await Swal.fire({
            title: "Enter Rejection Reason",
            input: "textarea",
            inputPlaceholder: "Provide a reason for rejection...",
            inputAttributes: {
                maxlength: "200",
                autocapitalize: "off",
                autocorrect: "off",
            },
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Reject Leave",
            cancelButtonText: "Cancel",
            inputValidator: (value) => {
                if (!value) {
                    return "Rejection reason is required!";
                }
            },
        });

        if (!reject_reason) {
            btn_loader(btn, false);
            return;
        }

        data.rejection_reason = reject_reason;
    }

    Swal.fire({
        title: `Are you sure you want to ${action} this leave?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: action === "approve" ? "#068f6d" : "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: `Yes, ${action} it!`,
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
               await leaveService.status(data);

getLeave(1, 'pending');
getLeave(1, 'declined');
getLeave(1, 'approved');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

