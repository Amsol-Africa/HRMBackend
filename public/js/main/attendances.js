import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AttendancesService from "/js/client/AttendancesService.js";

const requestClient = new RequestClient();
const attendancesService = new AttendancesService(requestClient);

window.getAttendances = async function (date = null) {
    try {
        let data = { date: date };
        const attendances = await attendancesService.fetch(data);
        $("#attendancesContainer").html(attendances);
        new DataTable('#attendancesTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.getMonthly = async function (month = null) {
    try {
        let data = { month: month };
        const attendances = await attendancesService.monthly(data);
        $("#attendancesContainer").html(attendances);
        new DataTable('#attendancesTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.getOvertime = async function (date = null) {
    try {
        let data = { date: date };
        const overtime = await attendancesService.overtime(data);
        $("#overtimeContainer").html(overtime);
        new DataTable('#overtimeTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.getClockins = async function () {
    try {
        const clockins = await attendancesService.clockins({});
        $("#clockinsContainer").html(clockins);
    } catch (error) {
        console.error("Error loading clock-ins:", error);
    }
};

window.clockIn = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    let formData;

    if ($("#clockInForm").length) {
        formData = new FormData(document.getElementById("clockInForm"));
        formData.delete('clock_in');
    } else {
        const employee = btn.data('employee');
        formData = new FormData();
        formData.append('employee_id', employee);
    }
    try {
        console.log(formData);
        await attendancesService.clockIn(formData);
    } catch (error) {
        console.error("Clock-in error:", error);
    } finally {
        getClockins();
        btn_loader(btn, false);
    }
};

window.clockOut = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const employee = btn.data('employee');
    const data = { employee: employee };

    try {
        await attendancesService.clockOut(data);
    } catch (error) {
        console.error("Clock-out error:", error);
    } finally {
        getClockins();
        btn_loader(btn, false);
    }
};

window.editAttendance = async function (btn) {
    btn = $(btn);

    const attendance = btn.data("attendance");
    const data = { attendance: attendance };

    try {
        const form = await attendancesService.edit(data);
        $('#attendancesFormContainer').html(form);
    } catch (error) {
        console.error("Edit attendance error:", error);
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
                Swal.fire('Success', 'Attendance deleted.', 'success');
                getAttendances();
            } catch (error) {
                console.error("Delete attendance error:", error);
                Swal.fire('Error', 'Failed to delete attendance.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};