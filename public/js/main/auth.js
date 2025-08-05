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

window.forgotPassword = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const form = $('#forgot-password-form');
    const formData = new FormData(form[0]);

    try {
        const response = await requestClient.post('/forgot-password', formData);
        await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message || 'A password reset link has been sent to your email.',
            confirmButtonText: 'OK',
        });

        form[0].reset();
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to send reset link. Please try again.',
        });
    } finally {
        btn_loader(btn, false);
    }
};

window.resetPassword = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const form = $('#reset-password-form');
    const formData = Object.fromEntries(new FormData(form[0]).entries());

    try {
        const response = await requestClient.post('/reset-password', formData);
        await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message || 'Your password has been reset successfully.',
            confirmButtonText: 'OK',
        });

        window.location.href = response.data?.redirect_url || '/login';
    } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to reset password. Please try again.',
        });
    } finally {
        btn_loader(btn, false);
    }
};

// Bind event listeners
$(document).ready(function () {
    $('#forgot-password-form').on('submit', function (e) {
        e.preventDefault();
        window.forgotPassword($('#forgot-password-button'));
    });

    $('#reset-password-form').on('submit', function (e) {
        e.preventDefault();
        window.resetPassword($('#reset-password-button'));
    });
});

window.logout = async function (btn) {
    try {
        await authService.logout({});
    } catch (error) {
    }
};