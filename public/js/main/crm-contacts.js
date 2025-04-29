import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('contactFilter');
    const contactsContainer = document.getElementById('contactsContainer');

    if (contactsContainer) {
        getContacts();
    }

    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getContacts(1, filterInput.value);
        }, 300));
    }
});

function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

window.getContacts = async function (page = 1, filter = '') {
    const container = $("#contactsContainer");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading contacts...</div>');
        const response = await requestClient.post('/crm/contacts/fetch', { page, filter });

        // Handle Blob response
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

        if ($('#contactsTable').length) {
            if ($.fn.DataTable.isDataTable('#contactsTable')) {
                $('#contactsTable').DataTable().destroy();
            }

            $('#contactsTable').DataTable({
                responsive: true,
                order: [[4, 'desc']],
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: "No contacts available",
                    loadingRecords: "Loading..."
                }
            });
        }
    } catch (error) {
        console.error("Error loading contacts:", error);
        container.html(`<div class="alert alert-danger">Error loading contacts: ${error.message}</div>`);
        toastr.error(`Failed to load contacts: ${error.message}`, "Error");
    }
};

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

window.deleteContact = async function (element) {
    const submissionId = element.dataset.submission;

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
                const response = await requestClient.post('/crm/contacts/destroy', { id: submissionId });
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
};