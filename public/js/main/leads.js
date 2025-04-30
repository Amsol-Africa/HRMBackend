import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('leadFilter');
    const leadsContainer = document.getElementById('leadsTable');
    const leadForm = document.getElementById('leadForm');
    const statusForm = document.getElementById('statusForm');
    const labelForm = document.getElementById('labelForm');
    const activityForm = document.getElementById('activityForm');
    const exportButtons = document.querySelectorAll('.export-leads');

    if (leadsContainer) {
        getLeads(); // Initial load
    }

    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getLeads(1, filterInput.value);
        }, 300));
    }

    if (leadForm) {
        leadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await createLead(leadForm);
        });
    }

    if (statusForm) {
        statusForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await updateLead(statusForm);
        });
    }

    if (activityForm) {
        activityForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await logActivity(activityForm);
        });
    }

    exportButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const format = button.dataset.format;
            await exportLeads(format);
        });
    });

    bindDeleteButtons();
});

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

window.getLeads = async function (page = 1, filter = '') {
    const container = $("#leadsTable");
    try {
        console.log('Fetching leads with filter:', filter, 'page:', page);
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading leads...</div>');

        const response = await requestClient.post('/crm/leads/fetch', { page, filter });
        let jsonResponse;

        if (response instanceof Blob) {
            const text = await response.text();
            jsonResponse = JSON.parse(text);
        } else {
            jsonResponse = response;
        }

        console.log('Fetch leads response:', jsonResponse);

        // Validate response
        if (typeof jsonResponse.data !== 'string') {
            throw new Error('Invalid response format: Expected HTML string for table data');
        }

        // Update container with new table HTML
        container.html(jsonResponse.data);

        // Handle server message
        const messageContainer = $('#leads-message');
        if (jsonResponse.message && messageContainer.length) {
            messageContainer.text(jsonResponse.message).show();
        } else if (messageContainer.length) {
            messageContainer.hide();
        }

        // Find the table
        const table = $('#leadsDataTable');
        if (!table.length) {
            console.warn('Table #leadsDataTable not found in DOM');
            container.html('<div class="alert alert-warning">Table not found. Please try again.</div>');
            return;
        }

        // Check if the table is empty
        const isEmpty = jsonResponse.count === 0 || table.data('empty') === 'true';
        console.log('Table empty state:', isEmpty, 'Count:', jsonResponse.count);

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#leadsDataTable')) {
            console.log('Destroying existing DataTable');
            $('#leadsDataTable').DataTable().destroy();
            table.empty(); // Clear any residual content
        }

        // Skip DataTables initialization for empty tables
        if (isEmpty) {
            console.log('Skipping DataTables initialization for empty table');
            bindDeleteButtons();
            return;
        }

        // Ensure the table is fully rendered before initializing DataTables
        await new Promise(resolve => requestAnimationFrame(resolve));

        console.log('Table HTML before DataTables:', table.html());

        // Initialize DataTables
        table.DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [{ targets: '_all', searchable: true }],
            language: {
                emptyTable: `No leads available.`,
                loadingRecords: "Loading..."
            }
        });

        console.log('DataTable initialized successfully');

        bindDeleteButtons();
    } catch (error) {
        console.error('Error loading leads:', error.message, error.stack);
        container.html(`
            <div class="alert alert-danger">
                Error loading leads: ${error.message}. Please try again later.
            </div>
        `);
        toastr.error(`Failed to load leads: ${error.message}`, 'Error');
    }
};

async function createLead(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const $submitButton = $(submitButton);
    try {
        btn_loader($submitButton, true);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        const response = await requestClient.post('/crm/leads/store', data);

        Swal.fire({
            title: 'Success!',
            text: 'Lead created successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            form.reset();
            const businessSlug = document.getElementById('businessSlug')?.value || 'default';
            window.location.href = form.dataset.redirect || `/business/${businessSlug}/crm/leads`;
        });
    } catch (error) {
        console.error("Error creating lead:", error);
        toastr.error(`Failed to create lead: ${error.message}`, 'Error');
    } finally {
        btn_loader($submitButton, false);
    }
}

async function updateLead(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const $submitButton = $(submitButton);
    try {
        btn_loader($submitButton, true);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        const response = await requestClient.post('/crm/leads/update', data);

        Swal.fire({
            title: 'Success!',
            text: 'Lead updated successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            location.reload();
        });
    } catch (error) {
        console.error("Error updating lead:", error);
        toastr.error(`Failed to update lead: ${error.message}`, 'Error');
    } finally {
        btn_loader($submitButton, false);
    }
}

async function logActivity(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const $submitButton = $(submitButton);
    try {
        btn_loader($submitButton, true);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        const response = await requestClient.post('/crm/lead-activities/store', data);

        Swal.fire({
            title: 'Success!',
            text: 'Activity logged successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            form.reset();
            location.reload();
        });
    } catch (error) {
        console.error("Error logging activity:", error);
        toastr.error(`Failed to log activity: ${error.message}`, 'Error');
    } finally {
        btn_loader($submitButton, false);
    }
}

async function exportLeads(format) {
    try {
        const businessSlug = document.getElementById('businessSlug')?.value ||
            document.querySelector('meta[name="business-slug"]')?.content;

        if (!businessSlug) {
            throw new Error('Business slug is missing. Please select a business.');
        }

        if (!['xlsx', 'csv', 'pdf'].includes(format)) {
            throw new Error('Invalid export format specified.');
        }

        const exportUrl = `/business/${businessSlug}/crm/reports/export/leads/${format}`;
        console.log(`Initiating lead export: ${exportUrl}`);
        console.log(`Debug: Sending GET request to ${exportUrl} with format=${format}, businessSlug=${businessSlug}`);

        const response = await requestClient.request(
            'GET',
            exportUrl,
            null,
            true
        );

        if (!(response instanceof Blob)) {
            throw new Error('Expected a Blob response for file download, received: ' + response.constructor.name);
        }

        const url = window.URL.createObjectURL(response);
        const a = document.createElement('a');
        a.href = url;
        a.download = `leads_report_${businessSlug}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error('Error exporting leads:', error.message, error.stack);
        if (error.message.includes('Non-JSON error')) {
            console.error('Received non-JSON response, possibly a server error or redirect');
            toastr.error('Failed to export leads: Invalid server response. Please check the server logs.', 'Error');
        } else if (error.message.includes('Something went wrong')) {
            toastr.error('Failed to export leads: Invalid report type or format. Please check the server configuration.', 'Error');
        } else if (error.message.includes('Unauthorized') || error.message.includes('401')) {
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session has expired. Please log in again.',
                icon: 'warning',
                confirmButtonText: 'OK',
            }).then(() => {
                window.location.href = '/login';
            });
        } else if (error.message.includes('Forbidden') || error.message.includes('403')) {
            toastr.error('You do not have permission to export leads.', 'Error');
        } else if (error.message.includes('Not Found') || error.message.includes('404')) {
            toastr.error('Export route not found. Please check the business context.', 'Error');
        } else {
            toastr.error(`Failed to export leads: ${error.message}`, 'Error');
        }
    }
}

function bindDeleteButtons() {
    document.querySelectorAll('.delete-lead').forEach(button => {
        button.removeEventListener('click', handleDeleteLead);
        button.addEventListener('click', handleDeleteLead);
    });
}

async function handleDeleteLead(event) {
    const leadId = event.target.dataset.id;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This lead will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post('/crm/leads/destroy', { id: leadId });
                await getLeads();
                Swal.fire(
                    'Deleted!',
                    'Lead has been deleted.',
                    'success'
                );
            } catch (error) {
                console.error("Error deleting lead:", error);
                toastr.error(`Failed to delete lead: ${error.message}`, 'Error');
            }
        }
    });
}