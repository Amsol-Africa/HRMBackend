<form action="" id="deductionsForm">

    <div class="row g-4">

        <div class="col-md-4">
            <label for="deduction_name">Deduction Name</label>
            <input type="text" name="deduction_name" id="deduction_name" class="form-control" placeholder="Enter Deduction Name">
        </div>

        <div class="col-md-4">
            <label for="deduction_type">Deduction Type</label>
            <select name="deduction_type" class="form-select" id="deduction_type" onchange="toggleDeductionTypeFields(this.value)">
                <option value="rate">Statutory</option>
                <option value="amount">Voluntary</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="deduction_type">Rate Type</label>
            <select name="deduction_type" class="form-select" id="deduction_type" onchange="toggleDeductionFields(this.value)">
                <option value="rate">Rate (%)</option>
                <option value="amount">Fixed Amount</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="calculation_basis">Calculation Basis</label>
            <select name="calculation_basis" class="form-select" id="calculation_basis">
                <option value="basic pay">Basic Pay</option>
                <option value="gross pay">Gross Pay</option>
                <option value="cash pay">Cash Pay</option>
                <option value="taxable pay">Taxable Pay</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="tax_application">Formula</label>
            <select name="tax_application" class="form-select" id="tax_application" required>
                <option value="">Select tax formula</option>
                @foreach ($formulas as $formula)
                    <option value="{{ $formula->slug }}">{{ $formula->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="deduction_class">Class</label>
            <select name="deduction_class" class="form-select" id="deduction_class" onchange="toggleBracketsSection(this.value)">
                <option value="yes">PAYE</option>
                <option value="no">retirement / Pension Scheme</option>
                <option value="no">Morgage</option>
                <option value="no">Loan</option>
                <option value="no">Insurance</option>
                <option value="no">Advabce</option>
                <option value="no">Absenteeism</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="tax_application">Tax Application</label>
            <select name="tax_application" class="form-select" id="tax_application" required>
                <option value="">Select tax application</option>
                <option value="before_tax">Deductible before tax</option>
                <option value="after_tax">Deductible after tax</option>
                <option value="from_amount">Deductible from amount</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="rounding_method">Rounding Method</label>
            <select name="rounding_method" class="form-select" id="rounding_method" required onchange="applyRoundingMethod()">
                <option value="">Select Rounding Method</option>
                <option value="round_up">Round Up</option>
                <option value="round_down">Round Down</option>
                <option value="round_nearest">Round to Nearest</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="decimal_places">Decimal Places</label>
            <select name="decimal_places" class="form-select" id="decimal_places" required onchange="applyRoundingMethod()">
                <option value="">Select Decimal Places</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="remmitance_date">Remmitance Date</label>
            <input type="text" name="remmitance_date" id="remmitance_date" class="form-control datepicker">
        </div>

        <div class="col-md-4">
            <label for="employer_name">Employer Name</label>
            <input type="text" name="employer_name" id="employer_name" class="form-control" placeholder="Employer Name">
        </div>

        <div class="col-md-4">
            <label for="employer_tax_no">Employer Tax Number</label>
            <input type="text" name="employer_tax_no" id="employer_tax_no" class="form-control" placeholder="e.g P*******">
        </div>

        <div class="col-md-4">
            <label for="employer_contribution">Employer Contibution</label>
            <div class="input-group">
                <input type="number" name="employer_contribution" id="employer_contribution" class="form-control" placeholder="Employer Contibution" min="0" max="100" step="0.01">
                <span class="input-group-text">%</span>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary w-100" onclick="savePayrollDeduction(this)">
            <i class="bi bi-check-circle"></i> Save Payroll Deduction
        </button>
    </div>

</form>
