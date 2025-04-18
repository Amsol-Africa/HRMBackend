import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DownloadsService from "/js/client/DownloadsService.js";

const requestClient = new RequestClient();
const downloadsService = new DownloadsService(requestClient);


window.download = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let payroll_id = $('#payroll_id').val();

    let formData = {
        file_type : btn.data('name'),
        payroll_id : payroll_id
    };

    try {
        await downloadsService.download(formData);
    } finally {
        btn_loader(btn, false);
    }
};
