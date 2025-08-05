import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import EmployeeReliefsService from "/js/client/EmployeeReliefs.js";

const requestClient = new RequestClient();
const employeeReliefsService = new EmployeeReliefsService(requestClient);

window.getEmployeeReliefs = async function (page = 1) {
    try {
        let data = { page: page };
        const response = await employeeReliefsService.fetch(data);
        $("#employeeReliefsContainer").html(response.html);
        $("#employeeReliefCount").text(response.count);
    } catch (error) {
        console.error("Error loading employee reliefs:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load employee reliefs.', 'error');
    }
};

window.saveEmployeeRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = $('#employeeReliefForm');
    let formData = new FormData(document.getElementById("employeeReliefForm"));

    try {
        if (formData.has("employee_relief_id")) {
            await employeeReliefsService.update(formData);
            Swal.fire('Success!', 'Employee relief updated successfully.', 'success');
        } else {
            await employeeReliefsService.save(formData);
            Swal.fire('Success!', 'Employee relief created successfully.', 'success');
        }
        form[0].reset();
        $('#employeeReliefFormContainer').html(await employeeReliefsService.edit({}));
        getEmployeeReliefs();
    } catch (error) {
        console.error("Error saving employee relief:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to save employee relief.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editEmployeeRelief = async function (btn) {
    btn = $(btn);
    const employeeRelief = btn.data("employee-relief");
    const data = { employee_relief_id: employeeRelief };

    try {
        const form = await employeeReliefsService.edit(data);
        $('#employeeReliefFormContainer').html(form);
    } catch (error) {
        console.error("Error editing employee relief:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deleteEmployeeRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const employeeRelief = btn.data("employee-relief");
    const data = { employee_relief_id: employeeRelief };

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await employeeReliefsService.delete(data);
                Swal.fire('Deleted!', 'Employee relief deleted successfully.', 'success');
                getEmployeeReliefs();
            } catch (error) {
                console.error("Error deleting employee relief:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete employee relief.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getEmployeeReliefs();
});