import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import UsersService from "/js/client/UsersService.js";

const requestClient = new RequestClient();
const usersService = new UsersService(requestClient);

window.getUsers = async function (role = null) {
    try {
        let data = {};
        if (role) data.role = role;
        const usersTable = await usersService.fetch(data);
        $("#usersContainer").html(usersTable);
        new DataTable('#usersTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.saveUser = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    formData.append("middle_name", $("#middle_name").val());
    formData.append("first_name", $("#first_name").val());
    formData.append("last_name", $("#last_name").val());
    formData.append("country", $("#country").val());
    formData.append("email", $("#email").val());
    formData.append("phone", $("#phone").val());
    formData.append("phone", $("#phone").val());
    formData.append("code", $("#code").val());
    formData.append("role", $("#role").val());
    formData.append("password", $("#password").val());

    if($("#role").val() === "agent") {
        // Agency Details
        formData.append("agency_name", $("#agency_name").val());
        formData.append("founded_in", $("#founded_in").val());
        formData.append("office", $("#office").val());
        formData.append("size", $("#size").val());

        // Collect Tour Types (Multi-select dropdown)
        const selectedTourTypes = $("#id_h5_multi").val();
        if (selectedTourTypes && selectedTourTypes.length > 0) {
            selectedTourTypes.forEach(type => {
                formData.append("tour_types[]", type);
            });
        }

        formData.append("destinations", $("#destinations").val());
        formData.append("about", $("#about").val());
    }

    try {
        await usersService.save(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.updateUser = async function (btn, userId) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    // User Details
    formData.append("middle_name", $("#middle_name").val());
    formData.append("first_name", $("#first_name").val());
    formData.append("last_name", $("#last_name").val());
    formData.append("country", $("#country").val());
    formData.append("email", $("#email").val());
    formData.append("phone", $("#phone").val());
    formData.append("code", $("#code").val());
    formData.append("role", $("#role").val());
    formData.append("password", $("#password").val());

    formData.append("user_id", userId);

    if($("#role").val() === "agent") {
        // Agency Details
        formData.append("agency_name", $("#agency_name").val());
        formData.append("founded_in", $("#founded_in").val());
        formData.append("office", $("#office").val());
        formData.append("size", $("#size").val());

        const selectedTourTypes = $("#id_h5_multi").val();
        if (selectedTourTypes && selectedTourTypes.length > 0) {
            selectedTourTypes.forEach(type => {
                formData.append("tour_types[]", type);
            });
        }

        formData.append("destinations", $("#destinations").val());
        formData.append("about", $("#about").val());
    }

    try {
        await usersService.update(userId, formData);
    } catch (error) {
        console.error("Error updating user:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteUser = async function (btn) {
    btn = $(btn);

    const user = btn.data("user");
    const data = { user: user };

    Swal.fire({
        text: "You are about to permanently delete a user account!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await usersService.delete(data);
                getUsers();
            } finally {
            }
        } else {
        }
    });
};


//profile

window.updateProfile = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("profileForm"));

    try {
        await usersService.updateProfile(formData);
    } finally {
        btn_loader(btn, false);
    }
};
