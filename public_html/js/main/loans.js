import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import LoansService from "/js/client/LoansService.js";

const requestClient = new RequestClient();
const loansService = new LoansService(requestClient);

window.getLoans = async function (page = 1) {
    try {
        let data = {page:page};
        const loansCards = await loansService.fetch(data);
        $("#loansContainer").html(loansCards);
        new DataTable('#loansTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.saveLoan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("loanForm"));

    try {
        if (formData.has('loan_id')) {
            await loansService.update(formData);
        } else {
            await loansService.save(formData);
        }
        getLoans();
    } finally {
        btn_loader(btn, false);
    }
};
window.editLoan = async function (btn) {
    btn = $(btn);

    const loan = btn.data("loan");
    const data = { loan: loan };

    try {
        const form = await loansService.edit(data);
        $('#loansFormContainer').html(form)
    } finally {
    }
};
window.deleteLoan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const loan = btn.data("loan");
    const data = { loan: loan };

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
                await loansService.delete(data);
                getLoans();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
