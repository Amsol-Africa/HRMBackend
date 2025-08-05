import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import SurveyService from "/js/client/SurveyService.js";

const requestClient = new RequestClient();
const surveyService = new SurveyService(requestClient);

window.getSurveys = async function (page = 1) {
    try {
        const response = await surveyService.fetch('/surveys/fetch', { page });
        $("#surveysContainer").html(response.html);
        $("#surveyCount").text(response.count);
    } catch (error) {
        console.error("Error loading surveys:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to load surveys.', 'error');
    }
};

window.saveSurvey = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const form = document.getElementById('surveyForm');
    const formData = new FormData(form);

    // Custom validation for multiple-choice options
    let isValid = true;
    document.querySelectorAll('.question-block').forEach((block, index) => {
        const questionType = block.querySelector(`select[name="questions[${index}][question_type]"]`).value;
        if (questionType === 'multiple_choice') {
            const options = block.querySelectorAll('.options-list input[name*="[options]"]');
            options.forEach((optionInput, optIndex) => {
                if (!optionInput.value.trim()) {
                    isValid = false;
                    optionInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = `Option ${optIndex + 1} is required for multiple-choice questions.`;
                    optionInput.parentElement.appendChild(errorDiv);
                } else {
                    optionInput.classList.remove('is-invalid');
                    const existingError = optionInput.parentElement.querySelector('.invalid-feedback');
                    if (existingError) existingError.remove();
                }
            });
        }
    });

    if (!isValid) {
        Swal.fire('Validation Error!', 'Please fill in all required options for multiple-choice questions.', 'error');
        btn_loader(btn, false);
        return;
    }

    try {
        const isUpdate = form.action.includes('/update');
        const surveyId = isUpdate ? form.action.match(/\/surveys\/(\d+)\/update/)?.[1] : null;
        const url = isUpdate ? `/surveys/${surveyId}/update` : '/surveys';

        const response = await surveyService.save(url, formData);
        Swal.fire('Success!', response.message || 'Survey saved successfully.', 'success');
        form.reset();
        surveyService.handleRedirect('/business/surveys');
    } catch (error) {
        console.error("Error saving survey:", error);
        Swal.fire('Error!', error.response?.data?.message || 'Failed to save survey.', 'error');
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteSurvey = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);
    const surveyId = btn.data("survey-id");

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
                await surveyService.delete(`/surveys/${surveyId}/destroy`, {
                    _token: document.querySelector('meta[name="csrf-token"]')?.content,
                });
                Swal.fire('Deleted!', 'Survey deleted successfully.', 'success');
                getSurveys();
            } catch (error) {
                console.error("Error deleting survey:", error);
                Swal.fire('Error!', error.response?.data?.message || 'Failed to delete survey.', 'error');
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    getSurveys();
});