document.addEventListener('DOMContentLoaded', function () {
    const supportForm = document.getElementById('supportForm');
    const issuesTable = $('#issuesTable').DataTable({
        ajax: {
            url: `/business/${currentBusinessSlug}/support/fetch`,
            dataSrc: 'data'
        },
        columns: [
            { data: 'title' },
            { data: 'description' },
            { data: 'status' },
            {
                data: 'screenshot',
                render: function (data) {
                    return data ? `<a href="${data}" target="_blank">View</a>` : 'N/A';
                }
            },
            { data: 'solved_by' },
            {
                data: null,
                render: function (data, type, row) {
                    return row.can_mark_solved && row.status === 'open' ?
                        `<button class="btn btn-sm btn-success mark-solved" data-id="${row.id}">Mark Solved</button>` : '';
                }
            }
        ]
    });

    supportForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(supportForm);

        RequestClient.post(`/business/${currentBusinessSlug}/support/store`, formData)
            .then(response => {
                toastr.success(response.message);
                supportForm.reset();
                issuesTable.ajax.reload();
            })
            .catch(error => {
                toastr.error('Failed to submit issue');
            });
    });

    $(document).on('click', '.mark-solved', function () {
        const issueId = $(this).data('id');
        RequestClient.post(`/business/${currentBusinessSlug}/support/${issueId}/mark-solved`, {})
            .then(response => {
                toastr.success(response.message);
                issuesTable.ajax.reload();
            })
            .catch(error => {
                toastr.error('Failed to mark issue as solved');
            });
    });
});