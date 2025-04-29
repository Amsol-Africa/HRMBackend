import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";

const requestClient = new RequestClient();

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('campaignFilter');
    const campaignsContainer = document.getElementById('campaignsTable');
    const campaignForm = document.getElementById('campaignForm');
    const visitsContainer = document.getElementById('visitsTable');
    const surveyContainer = document.getElementById('surveyTable');
    const exportButtons = document.querySelectorAll('.export-campaigns');

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

    // Handle export buttons
    exportButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const format = button.dataset.format;
            await exportCampaigns(format);
        });
    });

    // Bind copy link and delete buttons
    bindCopyLinkButtons();
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

// Fetch and display campaigns
window.getCampaigns = async function (page = 1, filter = '') {
    const container = $("#campaignsTable");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading campaigns...</div>');
        const response = await requestClient.post('/crm/campaigns/fetch', { page, filter });

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

        const tableId = type === 'visits' ? '#visitsDataTable' : '#surveyDataTable';
        if ($(tableId).length) {
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $(tableId).DataTable({
                responsive: true,
                pageLength: 10,
                order: [[type === 'visits' ? 5 : 6, 'desc']],
                columnDefs: [{ targets: '_all', searchable: true }],
                language: {
                    emptyTable: type === 'visits' ? 'No visits recorded' : 'No survey results available',
                    loadingRecords: 'Loading...'
                }
            });
        }

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
    const $submitButton = $(submitButton);
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

// Export campaigns
async function exportCampaigns(format) {
    try {
        const businessSlug = document.getElementById('businessSlug')?.value ||
            document.querySelector('meta[name="business-slug"]')?.content;

        if (!businessSlug) {
            throw new Error('Business slug is missing. Please select a business.');
        }

        const exportUrl = `/business/${businessSlug}/crm/reports/export/campaigns/${format}`;
        console.log(`Initiating campaign export: ${exportUrl}`);

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
        a.download = `campaigns_report_${businessSlug}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

    } catch (error) {
        console.error('Error exporting campaigns:', error.message, error.stack);
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
            toastr.error('Failed to export campaigns: Invalid server response. Please check the server logs.', 'Error');
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
            toastr.error('You do not have permission to export campaigns.', 'Error');
        } else if (errorMessage.includes('Not Found') || errorMessage.includes('404')) {
            toastr.error('Export route not found. Please check the business context.', 'Error');
        } else {
            toastr.error(`Failed to export campaigns: ${errorMessage}`, 'Error');
        }
    }
}

// Copy short link to clipboard
function bindCopyLinkButtons() {
    document.querySelectorAll('.copy-link').forEach(button => {
        button.removeEventListener('click', handleCopyLink);
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
        button.removeEventListener('click', handleDeleteCampaign);
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