import { btn_loader } from "https://amsol.anzar.co.ke/public_html/js/client/config.js";
import RequestClient from "https://amsol.anzar.co.ke/public_html/js/client/RequestClient.js";
import PayrollFormulasService from "https://amsol.anzar.co.ke/public_html/js/client/PayrollFormulasService.js";

const requestClient = new RequestClient();
const payrollFormulasService = new PayrollFormulasService(requestClient);

window.getPayrollFormulas = async function (page = 1) {
    try {
        let data = {page:page};
        const payrollFormulasTable = await payrollFormulasService.fetch(data);
        $("#payrollformulasContainer").html(payrollFormulasTable);

        var table = $('#payroll-formula-table');
        // dataTable(table, {
        //     "paging": false,
        //     "bInfo": false,
        //     "searching": false,
        //     "dom": 'lfrtip',
        //     "buttons": [],
        //     "language": {
        //         "emptyTable": "No data found",
        //         "search": "Search any column:"
        //     }
        // });

        // $('#payroll-formula-table').dataTable();
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

    let formData = new FormData(document.getElementById("payrollFormulasForm"));

    const formulaType = document.getElementById('formula_type').value;

    const brackets = document.querySelectorAll('[name^="brackets"]');
    brackets.forEach(bracket => {
        const rate = bracket.querySelector('[name$="[rate]"]');
        const amount = bracket.querySelector('[name$="[amount]"]');

        if (formulaType === 'amount') {
            if (rate) {
                formData.delete(rate.name);
            }
        } else {
            if (amount) {
                formData.delete(amount.name);
            }
        }
    });

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
