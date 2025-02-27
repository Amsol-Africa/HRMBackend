import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import PayrollFormulasService from "/js/client/PayrollFormulasService.js";

const requestClient = new RequestClient();
const payrollFormulasService = new PayrollFormulasService(requestClient);

window.loadFormulas = async function () {
    try {
        let data = {};
        const payrollFormulasForm = await payrollFormulasService.create(data);
        $("#payrollformulasFormContainer").html(payrollFormulasForm)
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};

window.getPayrollFormulas = async function (formula = 'nhif') {
    try {
        let data = {formula:formula};
        const payrollFormulasTable = await payrollFormulasService.fetch(data);
        $("#payrollformulasContainer").html(payrollFormulasTable);
        new DataTable('#payrollFormulas');
    } catch (error) {
        console.error("Error loading user data:", error);
    }
};
window.showFormula = async function (btn) {
    try {
        btn = $(btn);

        $('#payrollFormulaModal').modal('show')

        const payroll_formula = btn.data("payroll-formula");
        const data = { payroll_formula: payroll_formula };

        try {
            const formulaDetails = await payrollFormulasService.show(data);
            $('#payrollFormulaDetails').html(formulaDetails)
            $('#payrollFormulaModal').modal('show')
        } finally {
            btn_loader(btn, false);
        }

    } catch (error) {
        console.error("Error loading formula data:", error);
    }
};
window.savePayrollFormula = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formID = btn.data('form');

    let formData = new FormData(document.getElementById(formID));

    try {
        if (formData.has('payroll_formula_slug')) {
            await payrollFormulasService.update(formData);
        } else {
            await payrollFormulasService.save(formData);
        }
    } finally {
        btn_loader(btn, false);
    }
};
window.deletePayrollFormula = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const payroll_formula = btn.data("payroll-formula");
    const data = { payroll_formula: payroll_formula };

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
                await payrollFormulasService.delete(data);
                getPayrollFormulas();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
