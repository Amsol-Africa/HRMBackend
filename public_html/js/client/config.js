export const BASE_URL = document.querySelector('meta[name="base-url"]').getAttribute('content');
export const applicationServerKey = "BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0";

export function btn_loader(btn, isLoading) {
    if (btn) {
        if (isLoading) {
            btn.data('original-text', btn.html()).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
        } else {
            btn.html(btn.data('original-text')).prop('disabled', false);
        }
    }
}

export function data_table(table, options) {
    if (!table.length) {
        console.error('Table element not found for DataTables initialization.');
        return;
    }
    return table.DataTable(options);
}

export function handleRedirect(route) {
    if (route) {
        window.location.href = route;
    }
}
