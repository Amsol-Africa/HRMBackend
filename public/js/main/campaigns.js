import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('campaignFilter');
    const campaignsContainer = document.getElementById('campaignsTable');
    const campaignForm = document.getElementById('campaignForm');
    const visitsContainer = document.getElementById('visitsTable');
    const surveyContainer = document.getElementById('surveyTable');

    // Load campaigns if container exists
    if (campaignsContainer) {
        getCampaigns();
    }

    // Load analytics if containers exist
    if (visitsContainer) {
        getAnalytics(visitsContainer, 'visits');
    }
    if (surveyContainer) {
        getAnalytics(surveyContainer, 'survey');
    }

    // Handle filter input
    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await getCampaigns(1, filterInput.value);
        }, 300));
    }

    // Handle form submission
    if (campaignForm) {
        campaignForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await createCampaign(campaignForm);
        });
    }

    // Bind copy link buttons (initially and after table reload)
    bindCopyLinkButtons();
});

// Debounce function
function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

// Fetch and display campaigns
window.getCampaigns = async function (page = 1, filter = '') {
    const container = $("#campaignsTable");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading campaigns...</div>');
        const response = await requestClient.post('/crm/campaigns/fetch', { page, filter });

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

        // Initialize DataTable
        if ($('#campaignsDataTable').length) {
            if ($.fn.DataTable.isDataTable('#campaignsDataTable')) {
                $('#campaignsDataTable').DataTable().destroy();
            }

            $('#campaignsDataTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: "No campaigns available",
                    loadingRecords: "Loading..."
                }
            });
        }

        // Rebind event listeners
        bindCopyLinkButtons();
        bindDeleteButtons();
    } catch (error) {
        console.error("Error loading campaigns:", error);
        container.html(`<div class="alert alert-danger">Error loading campaigns: ${error.message}</div>`);
        toastr.error(`Failed to load campaigns: ${error.message}`, "Error");
    }
};

// Fetch and display analytics (visits or survey results)
window.getAnalytics = async function (container, type) {
    const $container = $(container);
    try {
        $container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading ' + type + '...</div>');
        const pathParts = window.location.pathname.split('/');
        const campaignId = pathParts[pathParts.length - 2];

        const response = await requestClient.post('/crm/campaigns/analytics/fetch', { campaign_id: campaignId, type });

        // Handle Blob response
        let jsonResponse;
        if (response instanceof Blob) {
            const text = await response.text();
            jsonResponse = JSON.parse(text);
        } else {
            jsonResponse = response;
        }

        if (typeof jsonResponse.data === 'string') {
            $container.html(jsonResponse.data);
        } else {
            throw new Error('Invalid response format from server');
        }

        // Initialize DataTable
        const tableId = type === 'visits' ? '#visitsDataTable' : '#surveyDataTable';
        if ($(tableId).length) {
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $(tableId).DataTable({
                responsive: true,
                pageLength: 10,
                order: [[type === 'visits' ? 5 : 6, 'desc']], // Sort by created_at
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: type === 'visits' ? 'No visits recorded' : 'No survey results available',
                    loadingRecords: 'Loading...'
                }
            });
        }

        // Rebind copy link buttons
        bindCopyLinkButtons();
    } catch (error) {
        console.error(`Error loading ${type}:`, error);
        $container.html(`<div class="alert alert-danger">Error loading ${type}: ${error.message}</div>`);
        toastr.error(`Failed to load ${type}: ${error.message}`, 'Error');
    }
};

// Create a new campaign
async function createCampaign(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const $submitButton = $(submitButton); // Convert to jQuery object for btn_loader
    try {
        btn_loader($submitButton, true);
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        const response = await requestClient.post('/crm/campaigns/store', data);

        Swal.fire({
            title: 'Success!',
            text: 'Campaign created successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            form.reset();
            window.location.href = form.dataset.redirect || '/business/{{ $currentBusiness->slug }}/crm/campaigns';
        });
    } catch (error) {
        console.error("Error creating campaign:", error);
        toastr.error(`Failed to create campaign: ${error.message}`, 'Error');
    } finally {
        btn_loader($submitButton, false);
    }
}

// Copy short link to clipboard
function bindCopyLinkButtons() {
    document.querySelectorAll('.copy-link').forEach(button => {
        button.removeEventListener('click', handleCopyLink); // Prevent multiple bindings
        button.addEventListener('click', handleCopyLink);
    });
}

function handleCopyLink(event) {
    const link = event.target.dataset.link;
    navigator.clipboard.writeText(link).then(() => {
        toastr.success('Link copied to clipboard!', 'Success');
    }).catch(() => {
        toastr.error('Failed to copy link.', 'Error');
    });
}

// Delete a campaign
function bindDeleteButtons() {
    document.querySelectorAll('.delete-campaign').forEach(button => {
        button.removeEventListener('click', handleDeleteCampaign); // Prevent multiple bindings
        button.addEventListener('click', handleDeleteCampaign);
    });
}

async function handleDeleteCampaign(event) {
    const campaignId = event.target.dataset.id;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This campaign will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await requestClient.post('/crm/campaigns/destroy', { id: campaignId });
                await getCampaigns();
                Swal.fire(
                    'Deleted!',
                    'Campaign has been deleted.',
                    'success'
                );
            } catch (error) {
                console.error("Error deleting campaign:", error);
                toastr.error(`Failed to delete campaign: ${error.message}`, 'Error');
            }
        }
    });
}