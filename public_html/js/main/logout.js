import RequestClient from "/js/client/RequestClient.js";
import AuthService from "/js/client/AuthService.js";

const requestClient = new RequestClient();
const authService = new AuthService(requestClient);

window.logout = async function(btn) {
    const data = { };
    try {
        await authService.logout(data);
    } finally {

    }
}
