<form action="" id="payrollFormulasForm">

    <div class="row g-4">

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="formula_name">Formula Name</label>
                <input type="text" name="formula_name" id="formula_name" class="form-control" placeholder="Enter Formula Name">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="formula_type">Formula Type</label>
                <select name="formula_type" class="form-select" id="formula_type" onchange="toggleFormulaFields(this.value)">
                    <option value="rate">Rate (%)</option>
                    <option value="amount">Fixed Amount</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="calculation_basis">Calculation Basis</label>
                <select name="calculation_basis" class="form-select" id="calculation_basis">
                    <option value="basic pay">Basic Pay</option>
                    <option value="gross pay">Gross Pay</option>
                    <option value="cash pay">Cash Pay</option>
                    <option value="taxable pay">Taxable Pay</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="is_progressive">Is Progressive</label>
                <select name="is_progressive" class="form-select" id="is_progressive" onchange="toggleBracketsSection(this.value)">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="minimum_amount">Minimum Amount</label>
                <input type="text" name="minimum_amount" id="minimum_amount" class="form-control" placeholder="Amount greater than e.g. 23000">
            </div>
        </div>

    </div>

    <!-- Brackets/Bands Section -->
    <div id="bracketsSection" class="mt-4" style="display: none;">
        <h5 class="mb-3">Define Brackets/Bands</h5>

        <div id="bracketsContainer">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="brackets[0][min]" class="form-control" placeholder="Min Amount">
                </div>

                <div class="col-md-4">
                    <input type="text" name="brackets[0][max]" class="form-control" placeholder="Max Amount">
                </div>

                <div class="col-md-4">
                    <input type="text" name="brackets[0][amount]" class="form-control" placeholder="Fixed Amount" style="display: none;">
                    <input type="text" name="brackets[0][rate]" class="form-control" placeholder="Rate (%)">
                </div>

            </div>
        </div>

        <button type="button" class="btn btn-link mt-3" onclick="addBracket()">+ Add Another Bracket</button>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary w-100" onclick="savePayrollFormula(this)">
            <i class="bi bi-check-circle"></i> Save Payroll Formula
        </button>
    </div>

</form>
