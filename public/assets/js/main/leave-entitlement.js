import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LeaveEntitlementsService from "/js/client/LeaveEntitlementsService.js";

const requestClient = new RequestClient();
const leaveEntitlementsService = new LeaveEntitlementsService(requestClient);

window.getLeaveEntitlements = async function (page = 1, leave_period = null) {
    console.log('getLeaveEntitlements called with page:', page, 'leave_period:', leave_period, 'at', new Date().toISOString());
    try {
        let data = { page: page, leave_period_slug: leave_period };
        console.log('Fetching data with:', data, 'Business Slug:', window.businessSlug);
        const response = await leaveEntitlementsService.fetch(data);
        console.log('Response received at', new Date().toISOString(), ':', response.substring(0, 200) + '...');

        const container = $('#leaveEntitlementsContainer');
        if (container.length) {
            container.html(response); // Directly insert the HTML table
            console.log('Container updated with HTML at', new Date().toISOString());
            if ($('#leaveEntitlementsTable').length) {
                new DataTable('#leaveEntitlementsTable'); // Initialize DataTable on the table
                console.log('DataTable initialized at', new Date().toISOString());
            } else {
                console.warn('Table #leaveEntitlementsTable not found at', new Date().toISOString());
            }
            container.find('.loader').hide(); // Hide the loader
            console.log('Loader hidden at', new Date().toISOString());
        } else {
            console.error('Element #leaveEntitlementsContainer not found at', new Date().toISOString());
        }
    } catch (error) {
        console.error('Error loading data at', new Date().toISOString(), ':', error.message, error.stack);
        $('#leaveEntitlementsContainer').html('<p>Error loading data. Please try again.</p>');
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
