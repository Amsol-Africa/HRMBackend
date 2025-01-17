<form action="" id="reliefsForm" class="needs-validation" novalidate>
    <div class="row">
        <div class="col-md-6">
            <label for="relief_name" class="form-label required">Relief Name</label>
            <input type="text" name="relief_name" id="relief_name" class="form-control" placeholder="e.g. Insurance Relief" required>
        </div>

        <div class="col-md-6">
            <label for="tax_application" class="form-label required">Relief Tax Application</label>
            <select name="tax_application" class="form-select" id="tax_application" required>
                <option value="">Select tax application</option>
                <option value="before_tax">Deductible before tax</option>
                <option value="after_tax">Deductible after tax</option>
                <option value="from_amount">Deductible from amount</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="relief_type" class="form-label required">Relief Type</label>
            <select name="relief_type" class="form-select" id="relief_type" onchange="toggleReliefFields(this.value)" required>
                <option value="">Select relief type</option>
                <option value="rate">Rate (%)</option>
                <option value="amount">Fixed Amount</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="comparison_method" class="form-label">Comparison Method</label>
            <select name="comparison_method" class="form-select" id="comparison_method">
                <option value="">Select comparison method (optional)</option>
                <option value="greatest">Greatest of</option>
                <option value="least">Least of</option>
            </select>
        </div>

        <div class="col-md-6 rate-field" style="display: none;">
            <label for="rate_percentage" class="form-label">Rate Percentage</label>
            <div class="input-group">
                <input type="number" name="rate_percentage" id="rate_percentage" class="form-control" placeholder="Enter rate" min="0" max="100" step="0.01">
                <span class="input-group-text">%</span>
            </div>
        </div>

        <div class="col-md-6 amount-field">
            <label for="fixed_amount" class="form-label">Fixed Amount</label>
            <div class="input-group">
                <span class="input-group-text">KES</span>
                <input type="number" name="fixed_amount" id="fixed_amount" class="form-control" placeholder="Enter amount" min="0" step="0.01">
            </div>
        </div>

        <div class="col-md-6">
            <label for="maximum_relief" class="form-label">Maximum Relief</label>
            <div class="input-group">
                <span class="input-group-text">KES</span>
                <input type="number" name="maximum_relief" id="maximum_relief" class="form-control" placeholder="e.g. 5,000" min="0" step="0.01">
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_mandatory" name="is_mandatory">
                <label class="form-check-label" for="is_mandatory">Mandatory Relief</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary w-100" id="saveReliefBtn">
            <i class="bi bi-check-circle me-2"></i>Save Relief Data
        </button>
    </div>
</form>
