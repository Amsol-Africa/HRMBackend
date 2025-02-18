import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AttendancesService from "/js/client/AttendancesService.js";

const requestClient = new RequestClient();
const attendancesService = new AttendancesService(requestClient);

window.getAttendances = async function (date = null) {
    try {
        let data = {date: date};
        const attendances = await attendancesService.fetch(data);
        $("#attendancesContainer").html(attendances);
        new DataTable('#attendancesTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.clockIn = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("clockInForm"));

    try {
        await attendancesService.clockIn(formData);
    } finally {
        btn_loader(btn, false);
    }
};
window.clockOut = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("clockOutForm"));

    try {
        await attendancesService.clockOut(formData);
    } finally {
        btn_loader(btn, false);
    }
};
window.editAttendance = async function (btn) {
    btn = $(btn);

    const attendance = btn.data("attendance");
    const data = { attendance: attendance };

    try {
        const form = await attendancesService.edit(data);
        $('#attendancesFormContainer').html(form)
    } finally {
    }
};
window.deleteAttendance = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const attendance = btn.data("attendance");
    const data = { attendance: attendance };

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
                await attendancesService.delete(data);
                getAttendances();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
