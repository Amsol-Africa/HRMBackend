import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ProfileService from "/js/client/ProfileService.js";

const requestClient = new RequestClient();
const profileService = new ProfileService(requestClient);

window.updateProfile = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("profileForm"));

    try {
        await profileService.save(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.changePassword = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("changePasswordForm"));

    try {
        await profileService.password(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteAccount = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("deleteAccountForm"));

    try {
        await profileService.delete(formData);
    } finally {
        btn_loader(btn, false);
    }
};
