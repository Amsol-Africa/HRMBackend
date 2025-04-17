import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import JobApplicantService from "/js/client/JobApplicantService.js";

const requestClient = new RequestClient();
const jobApplicantService = new JobApplicantService(requestClient);
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

document.addEventListener('DOMContentLoaded', () => {
    const filterInput = document.getElementById('applicantFilter');
    const jobFilter = document.getElementById('jobFilter');
    const locationFilter = document.getElementById('locationFilter');
    const jobApplicantsContainer = document.getElementById('jobApplicantsContainer');

    if (jobApplicantsContainer) {
        getJobApplicants();
    }

    if (filterInput) {
        filterInput.addEventListener('input', debounce(async () => {
            await filterJobApplicants(1, filterInput.value, jobFilter?.value, locationFilter?.value);
        }, 300));
    }

    if (jobFilter) {
        jobFilter.addEventListener('change', debounce(async () => {
            await filterJobApplicants(1, filterInput?.value, jobFilter.value, locationFilter?.value);
        }, 300));
    }

    if (locationFilter) {
        locationFilter.addEventListener('input', debounce(async () => {
            await filterJobApplicants(1, filterInput?.value, jobFilter?.value, locationFilter.value);
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

window.getJobApplicants = async function (page = 1) {
    const container = $("#jobApplicantsContainer");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading applicants...</div>');
        const data = { page, _token: csrfToken };
        const response = await jobApplicantService.fetch(data);
        container.html(response);
        initializeDataTable('#jobApplicantsTable');
    } catch (error) {
        console.error("Error loading applicants:", error);
        container.html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
    }
};

window.filterJobApplicants = async function (page = 1, filter = '', job_post_id = '', location = '') {
    const container = $("#jobApplicantsContainer");
    try {
        container.html('<div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Filtering applicants...</div>');
        const data = { page, filter, job_post_id, location, _token: csrfToken };

        // If no filters are applied, fetch all applicants
        if (!filter && !job_post_id && !location) {
            const response = await jobApplicantService.fetch({ page, _token: csrfToken });
            container.html(response);
        } else {
            const response = await jobApplicantService.filter(data);
            container.html(response);
        }
        initializeDataTable('#jobApplicantsTable');
    } catch (error) {
        console.error("Error filtering applicants:", error);
        container.html(`<div class="alert alert-danger">Error: ${error.message}</div>`);
    }
};

window.saveApplicant = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const form = document.getElementById("applicantForm");
    const formData = new FormData(form);
    formData.append('_token', csrfToken);
    try {
        if (formData.has('applicant_id')) {
            await jobApplicantService.update(formData);
            Swal.fire('Success', 'Applicant updated successfully!', 'success').then(() => {
                window.location.reload();
            });
        } else {
            await jobApplicantService.save(formData);
            Swal.fire('Success', 'Applicant saved successfully!', 'success').then(() => {
                window.location.href = `/business/${window.businessSlug}/applicants`;
            });
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to save applicant: ' + error.message, 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editJobApplicant = async function (applicant_id) {
    try {
        const form = await jobApplicantService.edit({ applicant_id, _token: csrfToken });
        $('#edit-applicant-form').html(form);
        $('#editApplicantModal').modal('show');
    } catch (error) {
        console.error('Error loading edit form:', error);
        toastr.error('Failed to load edit form: ' + error.message, "Error");
    }
};

window.deleteJobApplicant = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const applicant_id = btn.data("job-applicant");
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await jobApplicantService.delete({ applicant_ids: [applicant_id], _token: csrfToken });
                getJobApplicants();
                Swal.fire('Deleted!', 'Applicant deleted successfully.', 'success');
            } catch (error) {
                Swal.fire('Error', 'Failed to delete applicant: ' + error.message, 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.deleteJobApplicants = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const selectedIds = Array.from(document.querySelectorAll('input[name="applicant_ids[]"]:checked')).map(input => input.value);

    if (selectedIds.length === 0) {
        toastr.warning('Please select at least one applicant to delete.', "Warning");
        btn_loader(btn, false);
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete them!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await jobApplicantService.delete({ applicant_ids: selectedIds, _token: csrfToken });
                getJobApplicants();
                Swal.fire('Deleted!', 'Selected applicants deleted successfully.', 'success');
            } catch (error) {
                Swal.fire('Error', 'Failed to delete applicants: ' + error.message, 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.downloadDocument = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const applicant_id = btn.data("applicant-id");
    const media_id = btn.data("media-id");
    try {
        const response = await jobApplicantService.downloadDocument({
            applicant_id,
            media_id,
            _token: window.csrfToken
        });

        if (!(response instanceof Blob)) {
            throw new Error('Response is not a Blob');
        }

        const blob = response;
        const mimeToExt = {
            'application/pdf': 'pdf',
            'application/msword': 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx'
        };

        let extension = mimeToExt[blob.type] || 'bin';
        let filename = btn.text().trim().split(' ').slice(1).join(' ') ||

            `applicant_${applicant_id}_document_${media_id}.${extension}`;

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        toastr.success('Document downloaded successfully!', 'Success');
    } catch (error) {
        const errorMessage = error.response && error.response.data && error.response.data.message
            ? error.response.data.message
            : error.message || 'Unknown error';
        toastr.error('Failed to download document: ' + errorMessage, 'Error');
    } finally {
        btn_loader(btn, false);
    }
};

window.exportApplicants = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const filter = document.getElementById('applicantFilter')?.value || '';
    const job_post_id = document.getElementById('jobFilter')?.value || '';
    const location = document.getElementById('locationFilter')?.value || '';
    try {
        const blob = await jobApplicantService.export({ filter, job_post_id, location, _token: csrfToken });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `applicants_${new Date().toISOString()}.xlsx`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        toastr.success('Applicants exported successfully!', "Success");
    } catch (error) {
        toastr.error('Failed to export applicants: ' + error.message, "Error");
    } finally {
        btn_loader(btn, false);
    }
};

function initializeDataTable(selector) {
    if ($(selector).length) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
        }
        $(selector).DataTable({
            responsive: true,
            order: [[1, 'asc']],
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: '_all', searchable: true }
            ],
            language: {
                emptyTable: "No applicants available",
                loadingRecords: "Loading..."
            }
        });
    }
}