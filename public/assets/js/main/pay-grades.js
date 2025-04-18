import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import PayGradesService from "/js/client/PayGrades.js";

const requestClient = new RequestClient();
const payGradesService = new PayGradesService(requestClient);

window.getPayGrades = async function (page = 1) {
    try {
        let data = { page: page };
        const response = await payGradesService.fetch(data);
        $("#payGradesContainer").html(response.html);
        $("#payGradeCount").text(response.count);
    } catch (error) {
        console.error("Error loading pay grades:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load pay grades.', 'error');
    }
};

window.savePayGrade = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let form = $('#payGradeForm');
    let formData = new FormData(document.getElementById("payGradeForm"));

    try {
        if (formData.has("pay_grade_id")) {
            await payGradesService.update(formData);
            Swal.fire('Success!', 'Pay grade updated successfully.', 'success');
        } else {
            await payGradesService.save(formData);
            Swal.fire('Success!', 'Pay grade created successfully.', 'success');
        }
        form[0].reset();
        $('#payGradeFormContainer').html(await payGradesService.edit({}));
        getPayGrades();
    } catch (error) {
        console.error("Error saving pay grade:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to save pay grade.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.editPayGrade = async function (btn) {
    btn = $(btn);
    const payGrade = btn.data("pay-grade");
    const data = { pay_grade_id: payGrade };

    try {
        const form = await payGradesService.edit(data);
        $('#payGradeFormContainer').html(form);
    } catch (error) {
        console.error("Error editing pay grade:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load edit form.', 'error');
    }
};

window.deletePayGrade = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const payGrade = btn.data("pay-grade");
    const data = { pay_grade_id: payGrade };

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
                await payGradesService.delete(data);
                Swal.fire('Deleted!', 'Pay grade deleted successfully.', 'success');
                getPayGrades();
            } catch (error) {
                console.error("Error deleting pay grade:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete pay grade.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getPayGrades();
});