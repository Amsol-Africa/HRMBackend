import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import ReliefsService from "/js/client/ReliefsService.js";

const requestClient = new RequestClient();
const reliefsService = new ReliefsService(requestClient);

window.getReliefs = async function (page = 1) {
    try {
        let data = {page:page};
        const reliefsTable = await reliefsService.fetch(data);
        $("#reliefsContainer").html(reliefsTable);
        new DataTable('#reliefsTable');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.showRelief = async function (btn) {
    try {
        btn = $(btn);

        $('#payrollFormulaModal').modal('show')

        const payroll_formula = btn.data("payroll-formula");
        const data = { payroll_formula: payroll_formula };

        try {
            const formulaDetails = await reliefsService.show(data);
            $('#payrollFormulaDetails').html(formulaDetails)
            $('#payrollFormulaModal').modal('show')
        } finally {
            btn_loader(btn, false);
        }

    } catch (error) {
        console.error("Error loading formula data:", error);
    }
};

window.saveRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("reliefsForm"));

    try {
        if (formData.has('relief_slug')) {
            await reliefsService.update(formData);
        } else {
            await reliefsService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteRelief = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const relief_slug = btn.data("relief");
    const data = { relief_slug: relief_slug };

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
                await reliefsService.delete(data);
                getReliefs();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
