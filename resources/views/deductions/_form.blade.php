<form id="deductionForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($deduction))
    <input type="hidden" name="deduction_id" value="{{ $deduction->id }}">
    @endif
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label fw-medium text-dark">Deduction Name <span
                    class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $deduction->name ?? '' }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label fw-medium text-dark">Description</label>
            <textarea name="description" id="description"
                class="form-control">{{ $deduction->description ?? '' }}</textarea>
        </div>
        <div class="col-12">
            <label for="calculation_basis" class="form-label fw-medium text-dark">Calculation Basis <span
                    class="text-danger">*</span></label>
            <select name="calculation_basis" id="calculation_basis" class="form-select" required>
                <option value="" disabled {{ !isset($deduction) ? 'selected' : '' }}>Select Basis</option>
                <option value="basic_pay"
                    {{ isset($deduction) && $deduction->calculation_basis == 'basic_pay' ? 'selected' : '' }}>Basic Pay
                </option>
                <option value="gross_pay"
                    {{ isset($deduction) && $deduction->calculation_basis == 'gross_pay' ? 'selected' : '' }}>Gross Pay
                </option>
                <option value="cash_pay"
                    {{ isset($deduction) && $deduction->calculation_basis == 'cash_pay' ? 'selected' : '' }}>Cash Pay
                </option>
                <option value="taxable_pay"
                    {{ isset($deduction) && $deduction->calculation_basis == 'taxable_pay' ? 'selected' : '' }}>Taxable
                    Pay</option>
            </select>
            @error('calculation_basis')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="computation_method" class="form-label fw-medium text-dark">Computation Method <span
                    class="text-danger">*</span></label>
            <select name="computation_method" id="computation_method" class="form-select" required>
                <option value="" disabled {{ !isset($deduction) ? 'selected' : '' }}>Select Method</option>
                <option value="fixed"
                    {{ isset($deduction) && $deduction->computation_method == 'fixed' ? 'selected' : '' }}>Fixed Amount
                </option>
                <option value="rate"
                    {{ isset($deduction) && $deduction->computation_method == 'rate' ? 'selected' : '' }}>Rate (%)
                </option>
                <option value="formula"
                    {{ isset($deduction) && $deduction->computation_method == 'formula' ? 'selected' : '' }}>Formula
                </option>
            </select>
            @error('computation_method')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="amount_field"
            style="display: {{ isset($deduction) && $deduction->computation_method == 'fixed' ? 'block' : 'none' }};">
            <label for="amount" class="form-label fw-medium text-dark">Amount <span class="text-danger"
                    id="amount_required"
                    style="display: {{ isset($deduction) && $deduction->computation_method == 'fixed' ? 'inline' : 'none' }};">*</span></label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ $deduction->amount ?? '' }}"
                step="0.01" min="0"
                {{ isset($deduction) && $deduction->computation_method == 'fixed' ? 'required' : '' }}>
            @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="rate_field"
            style="display: {{ isset($deduction) && $deduction->computation_method == 'rate' ? 'block' : 'none' }};">
            <label for="rate" class="form-label fw-medium text-dark">Rate (%) <span class="text-danger"
                    id="rate_required"
                    style="display: {{ isset($deduction) && $deduction->computation_method == 'rate' ? 'inline' : 'none' }};">*</span></label>
            <input type="number" name="rate" id="rate" class="form-control" value="{{ $deduction->rate ?? '' }}"
                step="0.01" min="0" max="100"
                {{ isset($deduction) && $deduction->computation_method == 'rate' ? 'required' : '' }}>
            @error('rate')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="formula_field"
            style="display: {{ isset($deduction) && $deduction->computation_method == 'formula' ? 'block' : 'none' }};">
            <label for="formula" class="form-label fw-medium text-dark">Formula <span class="text-danger"
                    id="formula_required"
                    style="display: {{ isset($deduction) && $deduction->computation_method == 'formula' ? 'inline' : 'none' }};">*</span></label>
            <input type="text" name="formula" id="formula" class="form-control" value="{{ $deduction->formula ?? '' }}"
                placeholder="e.g. FringeBenefit(5%)"
                {{ isset($deduction) && $deduction->computation_method == 'formula' ? 'required' : '' }}>
            @error('formula')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="actual_amount" id="actual_amount" class="form-check-input" value="1"
                    {{ isset($deduction) && $deduction->actual_amount ? 'checked' : '' }}>
                <label for="actual_amount" class="form-check-label fw-medium text-dark">Actual Amount (Varies by
                    Employee)</label>
            </div>
            @error('actual_amount')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="fraction_to_consider" class="form-label fw-medium text-dark">Fraction to Consider <span
                    class="text-danger">*</span></label>
            <select name="fraction_to_consider" id="fraction_to_consider" class="form-select" required>
                <option value="" disabled {{ !isset($deduction) ? 'selected' : '' }}>Select Fraction</option>
                <option value="employee_only"
                    {{ isset($deduction) && $deduction->fraction_to_consider == 'employee_only' ? 'selected' : '' }}>
                    Employee Only</option>
                <option value="employee_and_employer"
                    {{ isset($deduction) && $deduction->fraction_to_consider == 'employee_and_employer' ? 'selected' : '' }}>
                    Employee & Employer</option>
            </select>
            @error('fraction_to_consider')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="limit" class="form-label fw-medium text-dark">Limit (Optional)</label>
            <input type="number" name="limit" id="limit" class="form-control" value="{{ $deduction->limit ?? '' }}"
                step="0.01" min="0">
            @error('limit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="round_off" class="form-label fw-medium text-dark">Round Off <span
                    class="text-danger">*</span></label>
            <select name="round_off" id="round_off" class="form-select" required>
                <option value="" disabled {{ !isset($deduction) ? 'selected' : '' }}>Select Rounding</option>
                <option value="round_off_up"
                    {{ isset($deduction) && $deduction->round_off == 'round_off_up' ? 'selected' : '' }}>Round Up
                </option>
                <option value="round_off_down"
                    {{ isset($deduction) && $deduction->round_off == 'round_off_down' ? 'selected' : '' }}>Round Down
                </option>
            </select>
            @error('round_off')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label for="decimal_places" class="form-label fw-medium text-dark">Decimal Places <span
                    class="text-danger">*</span></label>
            <select name="decimal_places" id="decimal_places" class="form-select" required>
                @for ($i = 0; $i <= 5; $i++) <option value="{{ $i }}"
                    {{ isset($deduction) && $deduction->decimal_places == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
            </select>
            @error('decimal_places')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="mt-4">
        <button type="button" class="btn btn-primary btn-modern" onclick="saveDeduction(this)">
            <i class="fa fa-save me-2"></i> {{ isset($deduction) ? 'Update Deduction' : 'Create Deduction' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            if (form.checkValidity()) {
                const method = $('#computation_method').val();
                const amount = $('#amount').val();
                const rate = $('#rate').val();
                const formula = $('#formula').val();

                if (method === 'fixed' && (!amount || parseFloat(amount) <= 0)) {
                    $('#amount').addClass('is-invalid');
                    $('#amount').siblings('.invalid-feedback').text(
                        'Amount is required and must be greater than 0.');
                    form.classList.add('was-validated');
                    return;
                }
                if (method === 'rate' && (!rate || parseFloat(rate) <= 0)) {
                    $('#rate').addClass('is-invalid');
                    $('#rate').siblings('.invalid-feedback').text(
                        'Rate is required and must be greater than 0.');
                    form.classList.add('was-validated');
                    return;
                }
                if (method === 'formula' && !formula) {
                    $('#formula').addClass('is-invalid');
                    $('#formula').siblings('.invalid-feedback').text('Formula is required.');
                    form.classList.add('was-validated');
                    return;
                }

                saveDeduction(form.querySelector('button[type="button"]'));
            }
            form.classList.add('was-validated');
        }, false);
    });

    $('#computation_method').on('change', function() {
        const method = $(this).val();
        const $amountField = $('#amount_field');
        const $rateField = $('#rate_field');
        const $formulaField = $('#formula_field');
        const $amountInput = $('#amount');
        const $rateInput = $('#rate');
        const $formulaInput = $('#formula');
        const $amountRequired = $('#amount_required');
        const $rateRequired = $('#rate_required');
        const $formulaRequired = $('#formula_required');

        $amountField.toggle(method === 'fixed');
        $rateField.toggle(method === 'rate');
        $formulaField.toggle(method === 'formula');

        $amountInput.prop('required', method === 'fixed');
        $rateInput.prop('required', method === 'rate');
        $formulaInput.prop('required', method === 'formula');
        $amountRequired.toggle(method === 'fixed');
        $rateRequired.toggle(method === 'rate');
        $formulaRequired.toggle(method === 'formula');

        if (method === 'fixed') {
            $rateInput.val('');
            $formulaInput.val('');
            $rateInput.removeClass('is-invalid');
            $formulaInput.removeClass('is-invalid');
        } else if (method === 'rate') {
            $amountInput.val('');
            $formulaInput.val('');
            $amountInput.removeClass('is-invalid');
            $formulaInput.removeClass('is-invalid');
        } else if (method === 'formula') {
            $amountInput.val('');
            $rateInput.val('');
            $amountInput.removeClass('is-invalid');
            $rateInput.removeClass('is-invalid');
        }
    });
})();
</script>
@endpush