<form id="deductionForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($deduction))
    <input type="hidden" name="deduction_id" value="{{ $deduction->id }}">
    @endif
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label fw-medium text-dark">Deduction Name</label>
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
            <label for="calculation_basis" class="form-label fw-medium text-dark">Calculation Basis</label>
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
            <label for="type" class="form-label fw-medium text-dark">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="" disabled {{ !isset($deduction) ? 'selected' : '' }}>Select Type</option>
                <option value="fixed" {{ isset($deduction) && $deduction->type == 'fixed' ? 'selected' : '' }}>Fixed
                    Amount</option>
                <option value="rate" {{ isset($deduction) && $deduction->type == 'rate' ? 'selected' : '' }}>Rate (%)
                </option>
            </select>
            @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="amount_field"
            style="display: {{ isset($deduction) && $deduction->type == 'fixed' ? 'block' : 'none' }};">
            <label for="amount" class="form-label fw-medium text-dark">Amount <span class="text-danger"
                    id="amount_required"
                    style="display: {{ isset($deduction) && $deduction->type == 'fixed' ? 'inline' : 'none' }};">*</span></label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ $deduction->amount ?? '' }}"
                step="0.01" min="0" {{ isset($deduction) && $deduction->type == 'fixed' ? 'required' : '' }}>
            @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="rate_field"
            style="display: {{ isset($deduction) && $deduction->type == 'rate' ? 'block' : 'none' }};">
            <label for="rate" class="form-label fw-medium text-dark">Rate (%) <span class="text-danger"
                    id="rate_required"
                    style="display: {{ isset($deduction) && $deduction->type == 'rate' ? 'inline' : 'none' }};">*</span></label>
            <input type="number" name="rate" id="rate" class="form-control" value="{{ $deduction->rate ?? '' }}"
                step="0.01" min="0" max="100" {{ isset($deduction) && $deduction->type == 'rate' ? 'required' : '' }}>
            @error('rate')
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
                    const type = $('#type').val();
                    const amount = $('#amount').val();
                    const rate = $('#rate').val();

                    // Custom validation before submission
                    if (type === 'fixed' && (!amount || parseFloat(amount) <= 0)) {
                        $('#amount').addClass('is-invalid');
                        $('#amount').siblings('.invalid-feedback').text(
                            'Amount is required and must be greater than 0.');
                        form.classList.add('was-validated');
                        return;
                    }
                    if (type === 'rate' && (!rate || parseFloat(rate) <= 0)) {
                        $('#rate').addClass('is-invalid');
                        $('#rate').siblings('.invalid-feedback').text(
                            'Rate is required and must be greater than 0.');
                        form.classList.add('was-validated');
                        return;
                    }

                    saveDeduction(form.querySelector('button[type="button"]'));
                }
                form.classList.add('was-validated');
            }, false);
        });

        $('#type').on('change', function() {
            const type = $(this).val();
            const $amountField = $('#amount_field');
            const $rateField = $('#rate_field');
            const $amountInput = $('#amount');
            const $rateInput = $('#rate');
            const $amountRequired = $('#amount_required');
            const $rateRequired = $('#rate_required');

            $amountField.toggle(type === 'fixed');
            $rateField.toggle(type === 'rate');

            $amountInput.prop('required', type === 'fixed');
            $rateInput.prop('required', type === 'rate');
            $amountRequired.toggle(type === 'fixed');
            $rateRequired.toggle(type === 'rate');

            if (type === 'fixed') {
                $rateInput.val('');
                $rateInput.removeClass('is-invalid');
            } else if (type === 'rate') {
                $amountInput.val('');
                $amountInput.removeClass('is-invalid');
            }
        });
    })();
</script>
@endpush