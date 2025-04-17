import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DeductionsService from "/js/client/Deductions.js";

const requestClient = new RequestClient();
const deductionsService = new DeductionsService(requestClient);

window.getDeductions = async function (page = 1) {
    try {
        let data = { page: page };
        const response = await deductionsService.fetch(data);
        $("#deductionsContainer").html(response.html);
        $("#deductionCount").text(response.count);
    } catch (error) {
        console.error("Error loading deductions:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load deductions.', 'error');
    }
};

window.saveDeduction = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("deductionForm"));

    try {
        if (formData.get("deduction_id")) {
            await deductionsService.update(formData);
        } else {
            await deductionsService.save(formData);
        }
        $('#deductionForm')[0].reset();
        $('#deductionFormContainer').html(await deductionsService.edit({}));
        $('#deductionForm').removeClass('was-validated');
        setTimeout(() => getDeductions(), 100);
    } catch (error) {
        console.error("Error saving deduction:", error);
        const errors = error.response?.data?.errors || [error.response?.data?.message || 'Failed to save deduction.'];
        Swal.fire('Error!', errors.join('<br>'), 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editDeduction = async function (btn) {
    btn = $(btn);
    const deduction = btn.data("deduction");
    const data = { deduction_id: deduction };

    try {
        const form = await deductionsService.edit(data);
        $('#deductionFormContainer').html(form);
        $('#deductionForm').removeClass('was-validated');
    } catch (error) {
        console.error("Error editing deduction:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deleteDeduction = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const deduction = btn.data("deduction");
    const data = { deduction_id: deduction };

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
                await deductionsService.delete(data);
                getDeductions();
            } catch (error) {
                console.error("Error deleting deduction:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete deduction.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getDeductions();
});