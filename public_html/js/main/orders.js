import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import OrdersService from "/js/client/OrdersService.js";

const requestClient = new RequestClient();
const ordersService = new OrdersService(requestClient);

window.getOrders = async function (page = 1, agent = null) {
    try {
        let data = { page: page, agent: agent };
        const ordersTable = await ordersService.fetch(data);
        $("#ordersContainer").html(ordersTable);
        new DataTable('#ordersTable');
    } catch (error) {
        console.error("Error loading orders:", error);
    }
};

window.saveOrder = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("user_id", $("#user_id").val());
    formData.append("orderable_type", $("#orderable_type").val());
    formData.append("orderable_id", $("#orderable_id").val());
    formData.append("bid_id", $("#bid_id").val());
    formData.append("description", $("#description").val());
    formData.append("amount", $("#amount").val());
    formData.append("discount", $("#discount").val() || 0);

    try {
        await ordersService.save(formData);
        getOrders();
    } catch (error) {
        console.error("Error saving order:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteOrder = async function (orderId, btn) {
    btn = $(btn);
    btn_loader(btn, true);

    try {
        await ordersService.delete(orderId);
        getOrders();
    } catch (error) {
        console.error("Error deleting order:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.updateOrderStatus = async function (orderId, newStatus, btn) {
    btn = $(btn);
    btn_loader(btn, true);

    try {
        await ordersService.updateStatus(orderId, newStatus);
        getOrders();
    } catch (error) {
        console.error("Error updating order status:", error);
    } finally {
        btn_loader(btn, false);
    }
};
