import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('contactFilter');
    const contactsContainer = document.getElementById('contactsTable');
    const contactForm = document.getElementById('contactForm');
    const exportButtons = document.querySelectorAll('.export-contacts');

    // Load contacts if container exists
    if (contactsContainer) {
        getContacts();
    }

    // Handle filter input
    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getContacts(1, filterInput.value);
        }, 300));
    }

    // Handle form submission
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await createContact(contactForm);
        });
    }

    // Handle export buttons
    exportButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const format = button.dataset.format;
            await exportContacts(format);
        });
    });

    bindDeleteButtons();
});

// Debounce function
function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

// Fetch and display contacts
window.getContacts = async function (page = 1, filter = '') {
    const container = $("#contactsTable");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading contacts...</div>');
        const response = await requestClient.post('/crm/contacts/fetch', { page, filter });

        let jsonResponse;
        if (response instanceof Blob) {
            const text = await response.text();
            jsonResponse = JSON.parse(text);
        } else {
            jsonResponse = response;
        }

        if (typeof jsonResponse.data === 'string') {
            container.html(jsonResponse.data);
        } else {
            throw new Error('Invalid response format from server');
        }

        if ($('#contactsDataTable').length) {
            if ($.fn.DataTable.isDataTable('#contactsDataTable')) {
                $('#contactsDataTable').DataTable().destroy();
            }

            $('#contactsDataTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[4, 'desc']],
                columnDefs: [
                    { targets: '_all', searchable: true },
                    { responsivePriority: 1, targets: 1 }, // Name
                    { responsivePriority: 2, targets: 2 }, // Email
                    { responsivePriority: 3, targets: 6 }, // Status
                    { responsivePriority: 4, targets: 8 }  // Actions
                ],
                language: {
                    emptyTable: "No contacts available",
                    loadingRecords: "Loading..."
                }
            });
        }

        bindDeleteButtons();
    } catch (error) {
        console.error("Error loading contacts:", error);
        container.html(`<div class="alert alert-danger">Error loading contacts: ${error.message}</div>`);
        toastr.error(`Failed to load contacts: ${error.message}`, "Error");
    }
};

// Update contact status
window.updateContactStatus = async function (element) {
    const submissionId = element.dataset.submission;
    const status = element.value;

    try {
        const response = await requestClient.post('/crm/contacts/update', { id: submissionId, status });
        await getContacts();
        toastr.success('Contact status updated successfully.', 'Success');
    } catch (error) {
        console.error("Error updating contact status:", error);
        toastr.error(`Failed to update contact status: ${error.message}`, 'Error');
    }
};

// Create a new contact
async function createContact(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const $submitButton = $(submitButton);
    try {
        btn_loader($submitButton, true);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        const response = await requestClient.post('/crm/contacts/store', data);

        Swal.fire({
            title: 'Success!',
            text: 'Contact created successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            form.reset();
            const businessSlug = document.getElementById('businessSlug')?.value || 'default';
            window.location.href = form.dataset.redirect || `/business/${businessSlug}/crm/contacts`;
        });
    } catch (error) {
        console.error("Error creating contact:", error);
        toastr.error(`Failed to create contact: ${error.message}`, 'Error');
    } finally {
        btn_loader($submitButton, false);
    }
};

// Export contacts
async function exportContacts(format) {
    try {
        const businessSlug = document.getElementById('businessSlug')?.value ||
            document.querySelector('meta[name="business-slug"]')?.content;

        if (!businessSlug) {
            throw new Error('Business slug is missing. Please select a business.');
        }

        if (!['xlsx', 'csv', 'pdf'].includes(format)) {
            throw new Error('Invalid export format specified.');
        }

        const exportUrl = `/business/${businessSlug}/crm/reports/export/contacts/${format}`;
        console.log(`Initiating contact export: ${exportUrl}`);

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
        a.download = `contacts_report_${businessSlug}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error('Error exporting contacts:', error.message, error.stack);
        let errorMessage = error.message;
        if (error.message.includes('Request failed')) {
            try {
                const response = await error.response.json();
                errorMessage = response.error || response.message || errorMessage;
            } catch {
                // Fallback to generic message
            }
        }
        if (errorMessage.includes('Non-JSON error')) {
            console.error('Received non-JSON response, possibly a server error or redirect');
            toastr.error('Failed to export contacts: Invalid server response. Please check the server logs.', 'Error');
        } else if (errorMessage.includes('Unauthorized') || errorMessage.includes('401')) {
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session has expired. Please log in again.',
                icon: 'warning',
                confirmButtonText: 'OK',
            }).then(() => {
                window.location.href = '/login';
            });
        } else if (errorMessage.includes('Forbidden') || errorMessage.includes('403')) {
            toastr.error('You do not have permission to export contacts.', 'Error');
        } else if (errorMessage.includes('Not Found') || errorMessage.includes('404')) {
            toastr.error('Export route not found. Please check the business context.', 'Error');
        } else {
            toastr.error(`Failed to export contacts: ${errorMessage}`, 'Error');
        }
    }
}

// Delete a contact
function bindDeleteButtons() {
    document.querySelectorAll('.delete-contact').forEach(button => {
        button.removeEventListener('click', handleDeleteContact);
        button.addEventListener('click', handleDeleteContact);
    });
}

async function handleDeleteContact(event) {
    const submissionId = event.target.closest('.delete-contact').dataset.id;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This contact submission will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post('/crm/contacts/destroy', { id: submissionId });
                await getContacts();
                Swal.fire(
                    'Deleted!',
                    'Contact submission has been deleted.',
                    'success'
                );
            } catch (error) {
                console.error("Error deleting contact:", error);
                toastr.error(`Failed to delete contact: ${error.message}`, 'Error');
            }
        }
    });
}