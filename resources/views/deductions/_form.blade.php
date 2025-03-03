<form action="" id="deductionsForm">

    <div class="row g-4">

        <div class="form-group">
            <label for="location">Location (Optional if for main business)</label>
            <select name="location" class="form-select" id="deduction_location">
                <option value="">Select Location</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->slug }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="deduction_name">Deduction Name</label>
            <input type="text" name="name" id="deduction_name" class="form-control" placeholder="Enter Deduction Name">
        </div>

        <div class="form-group">
            <label for="calculation_basis">Calculation Basis</label>
            <select name="calculation_basis" class="form-select" id="calculation_basis">
                <option value="basic_pay">Basic Pay</option>
                <option value="gross_pay">Gross Pay</option>
                <option value="cash_pay">Cash Pay</option>
                <option value="taxable_pay">Taxable Pay</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">Optional...</textarea>
        </div>

    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary w-100" onclick="saveDeductions(this)">
            <i class="bi bi-check-circle me-1"></i> Save Deduction
        </button>
    </div>

</form>
