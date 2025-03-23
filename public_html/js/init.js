// Initialize Toastr options
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: false,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};
function initializeDatepicker() {
    $(".datepicker").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        allowInput: true,
    });
}
function dataTable(table, options) {
    if (!table || !table.dataTable) {
        throw new Error("The first argument must be a valid jQuery object");
    }

    options = $.extend(
        {
            scrollX: true,
            ordering: true,
            lengthChange: true,
            autoWidth: true,
            pageLength: 10,
            columnDefs: [{ searchable: true, targets: 0 }],
            dom: '<"d-flex justify-content-between align-items-center"lBf>tip',
            buttons: [
                {
                    extend: "print",
                    text: '<i class="fa-solid fa-print" title="print"></i>',
                    exportOptions: {
                        columns: ":visible",
                    },
                },
                {
                    extend: "csv",
                    text: '<i class="fa-solid fa-file-csv" title="csv"></i>',
                    exportOptions: {
                        columns: ":visible",
                    },
                },
                {
                    extend: "excel",
                    text: '<i class="fa-solid fa-file-excel" title="excel"></i>',
                    exportOptions: {
                        columns: ":visible",
                    },
                },
            ],
            language: {
                emptyTable: "No data available in the table",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                search: "Search:",
                paginate: {
                    previous: "Previous",
                    next: "Next",
                },
            },
            drawCallback: function (settings) {
                var api = this.api();
                var pageInfo = api.page.info();
                var $pagination = $(this)
                    .closest(".dataTables_wrapper")
                    .find(".dataTables_paginate");
                if (pageInfo.pages <= 1) {
                    $pagination.hide();
                } else {
                    $pagination.show();
                }
            },
        },
        options
    );

    table.dataTable(options);
}
