import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import RostersService from "/js/client/RostersService.js";

const requestClient = new RequestClient();
const rostersService = new RostersService(requestClient);

let currentView = 'table';

window.getRosters = async function (page = 1) {
    try {
        const filters = {
            page: page,
            department_id: $('#filterDepartment').val(),
            job_category_id: $('#filterJobCategory').val(),
            location_id: $('#filterLocation').val(),
            employee_id: $('#filterEmployee').val(),
            view_type: currentView,
        };

        const response = await rostersService.fetch(filters);
        $("#rostersContainer").html(response);

        if (currentView === 'table') {
            const table = new DataTable('#rostersTable', {
                dom: '<"d-flex justify-content-between align-items-center mb-3"lBf>rt<"d-flex justify-content-between mt-3"ip>',
                order: [[7, 'desc']],
                lengthMenu: [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
                pageLength: 10,
                buttons: [
                    { extend: 'copy', className: 'btn btn-outline-primary btn-sm', exportOptions: { columns: ':not(:first-child, :last-child)' } },
                    { extend: 'csv', className: 'btn btn-outline-secondary btn-sm', exportOptions: { columns: ':not(:first-child, :last-child)' } },
                    { extend: 'excel', className: 'btn btn-outline-success btn-sm', exportOptions: { columns: ':not(:first-child, :last-child)' } },
                    { extend: 'pdf', className: 'btn btn-outline-danger btn-sm', exportOptions: { columns: ':not(:first-child, :last-child)' } },
                    { extend: 'print', className: 'btn btn-outline-info btn-sm', exportOptions: { columns: ':not(:first-child, :last-child)' } },
                    {
                        text: '<i class="fas fa-bell"></i> Notify Selected',
                        className: 'btn btn-outline-warning btn-sm',
                        action: notifySelectedAssignments
                    }
                ]
            });

            table.on('click', 'tbody tr', function (e) {
                if (!$(e.target).closest('.btn').length) {
                    $(this).toggleClass('selected');
                    $(this).find('.selectRow').prop('checked', $(this).hasClass('selected'));
                }
            });

            $('#selectAll').on('change', function () {
                const isChecked = $(this).is(':checked');
                table.rows().nodes().to$().find('.selectRow').prop('checked', isChecked).closest('tr').toggleClass('selected', isChecked);
            });

            window.getSelectedAssignments = function () {
                return table.rows('.selected').nodes().to$().find('.selectRow').map((_, el) => $(el).data('id')).get();
            };
        }

        if (currentView !== 'calendar') {
            initCalendar();
        }
    } catch (error) {
        console.error("Error loading roster data:", error);
        Swal.fire("Error", "Failed to load rosters.", "error");
    }
};

window.saveRoster = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const form = document.getElementById("rostersForm");
    const formData = new FormData(form);

    try {
        if (formData.get('roster_slug')) {
            await rostersService.update(formData);
            toastr.success("Roster updated successfully.", "Success");
        } else {
            await rostersService.save(formData);
            toastr.success("Roster created successfully.", "Success");
        }

        form.reset();
        $("#roster_slug").val("");
        $("#card-header").text("Add New Roster");
        $("#submitButton").html('<i class="bi bi-check-circle"></i> Save Roster');
        $("#assignmentsContainer").html($('.assignment-row:first').clone().find('input, select, textarea').val('').end());
        getRosters();
    } catch (error) {
        console.error("Error saving roster:", error);
        Swal.fire("Error", error.response?.data?.message || "Failed to save roster.", "error");
    } finally {
        btn_loader(btn, false);
    }
};

window.editRoster = async function (btn) {
    btn = $(btn);
    const roster = btn.data("roster");

    try {
        const form = await rostersService.edit({ slug: roster });
        $('#rostersFormContainer').html(form);
        $("#submitButton").html('<i class="bi bi-check-circle"></i> Update Roster');
        $("#card-header").text("Edit Roster");
        $(".date-picker").flatpickr({ dateFormat: "Y-m-d" });
    } catch (error) {
        console.error("Error editing roster:", error);
        Swal.fire("Error", "Failed to edit roster.", "error");
    }
};

window.deleteRoster = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const roster = btn.data("roster");

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#1a73e8",
        cancelButtonColor: "#dc3545",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await rostersService.delete({ slug: roster });
                toastr.info("Roster deleted successfully.", "Success");
                getRosters();
            } catch (error) {
                console.error("Error deleting roster:", error);
                Swal.fire("Error", "Failed to delete roster.", "error");
            }
        }
        btn_loader(btn, false);
    });
};

window.notifySelectedAssignments = async function () {
    const selectedIds = window.getSelectedAssignments();

    if (!selectedIds.length) {
        Swal.fire("No assignments selected!", "Please select assignments to notify.", "warning");
        return;
    }

    Swal.fire({
        title: "Notify selected assignments?",
        text: `You are about to notify ${selectedIds.length} assignment(s).`,
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#1a73e8",
        cancelButtonColor: "#dc3545",
        confirmButtonText: "Yes, notify them!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await rostersService.notify({ ids: selectedIds });
                Swal.fire("Notified!", "The selected assignments have been notified.", "success");
                getRosters();
            } catch (error) {
                console.error("Error notifying assignments:", error);
                Swal.fire("Error", "Failed to notify assignments.", "error");
            }
        }
    });
};

window.generateReport = async function () {
    try {
        const filters = {
            department_id: $('#filterDepartment').val(),
            job_category_id: $('#filterJobCategory').val(),
            location_id: $('#filterLocation').val(),
            employee_id: $('#filterEmployee').val(),
            type: 'all'
        };
        const report = await rostersService.generateReport(filters);
        Swal.fire({
            title: "Roster Report",
            html: report,
            icon: "success",
            width: '80%',
            showConfirmButton: true,
            confirmButtonText: "Close"
        });
    } catch (error) {
        console.error("Error generating report:", error);
        Swal.fire("Error", "Failed to generate report.", "error");
    }
};

window.toggleView = function () {
    if (currentView === 'calendar') {
        currentView = 'table';
        $('#rostersContainer').show();
        $('#calendarContainer').hide();
        $('#toggleView').html('<i class="fas fa-table"></i> Table View');
    } else if (currentView === 'table') {
        currentView = 'cards';
        $('#rostersContainer').show();
        $('#calendarContainer').hide();
        $('#toggleView').html('<i class="fas fa-th"></i> Cards View');
    } else {
        currentView = 'calendar';
        $('#rostersContainer').hide();
        $('#calendarContainer').show();
        $('#toggleView').html('<i class="fas fa-table"></i> Table View');
    }
    getRosters();
};

function initCalendar() {
    const calendarEl = document.getElementById('calendarContainer');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: async function (fetchInfo, successCallback, failureCallback) {
            try {
                const filters = {
                    department_id: $('#filterDepartment').val(),
                    job_category_id: $('#filterJobCategory').val(),
                    location_id: $('#filterLocation').val(),
                    employee_id: $('#filterEmployee').val(),
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                };
                const response = await rostersService.fetchCalendarEvents(filters);
                successCallback(response.events);
            } catch (error) {
                console.error("Error loading calendar events:", error);
                failureCallback(error);
            }
        },
        eventClick: function (info) {
            Swal.fire({
                title: info.event.title,
                html: `
                    <p><strong>Employee:</strong> ${info.event.extendedProps.employeeName}</p>
                    <p><strong>Department:</strong> ${info.event.extendedProps.departmentName}</p>
                    <p><strong>Shift:</strong> ${info.event.extendedProps.shiftName || 'N/A'}</p>
                    <p><strong>Leave:</strong> ${info.event.extendedProps.leaveName || 'N/A'}</p>
                    <p><strong>Overtime:</strong> ${info.event.extendedProps.overtimeHours} hrs</p>
                `,
                icon: 'info',
                confirmButtonColor: '#1a73e8'
            });
        }
    });
    calendar.render();
}

$('#exportCsv').click(async () => {
    const filters = {
        department_id: $('#filterDepartment').val(),
        job_category_id: $('#filterJobCategory').val(),
        location_id: $('#filterLocation').val(),
        employee_id: $('#filterEmployee').val(),
        format: 'csv'
    };
    await rostersService.export(filters);
});

$('#exportPdf').click(async () => {
    const filters = {
        department_id: $('#filterDepartment').val(),
        job_category_id: $('#filterJobCategory').val(),
        location_id: $('#filterLocation').val(),
        employee_id: $('#filterEmployee').val(),
        format: 'pdf'
    };
    await rostersService.export(filters);
});

$('#exportExcel').click(async () => {
    const filters = {
        department_id: $('#filterDepartment').val(),
        job_category_id: $('#filterJobCategory').val(),
        location_id: $('#filterLocation').val(),
        employee_id: $('#filterEmployee').val(),
        format: 'excel'
    };
    await rostersService.export(filters);
});