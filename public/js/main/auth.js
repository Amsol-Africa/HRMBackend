import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import AuthService from "/js/client/AuthService.js";

const requestClient = new RequestClient();
const authService = new AuthService(requestClient);

window.login = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const loginData = {
        email: $('#email').val(),
        password: $('#password').val(),
        remember: $('#remember').is(':checked')
    };

    try {
        await authService.login(loginData);
        $('#email, #password').val('');
    } catch (error) {
    } finally {
        btn_loader(btn, false);
    }
};

window.register = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("registerForm"));

    try {
        await authService.register(formData);
    } catch (error) {
    } finally {
        btn_loader(btn, false);
    }
};

window.logout = async function (btn) {
    try {
        await authService.logout({});
    } catch (error) {
    }
};