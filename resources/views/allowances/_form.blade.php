<form action="" id="allowancesForm">

    <div class="row g-4">

        <div class="col-md-4">
            <label for="allowance_name">Allowance Name</label>
            <input type="text" name="allowance_name" id="allowance_name" class="form-control" placeholder="Enter Allowance Name">
        </div>

        <div class="col-md-4">
            <label for="allowance_type">Rate Type</label>
            <select name="allowance_type" class="form-select" id="allowance_type" onchange="toggleAllowanceFields(this.value)">
                <option value="rate">Rate (%)</option>
                <option value="amount">Fixed Amount</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="calculation_basis">Calculated On</label>
            <select name="calculation_basis" class="form-select" id="calculation_basis">
                <option value="basic pay">Basic Pay</option>
                <option value="gross pay">Gross Pay</option>
                <option value="cash pay">Cash Pay</option>
                <option value="taxable pay">Taxable Pay</option>
            </select>
        </div>

    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary w-100" onclick="savePayrollAllowance(this)">
            <i class="bi bi-check-circle"></i> Save Payroll Allowance
        </button>
    </div>

</form>
