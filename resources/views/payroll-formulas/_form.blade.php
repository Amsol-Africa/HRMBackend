<form id="formulaForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($formula))
    <input type="hidden" name="formula_id" value="{{ $formula->id }}">
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label fw-medium text-dark">Formula Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $formula->name ?? '' }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label for="formula_type" class="form-label fw-medium text-dark">Formula Type</label>
            <select name="formula_type" id="formula_type" class="form-select" required>
                <option value="rate" {{ isset($formula) && $formula->formula_type == 'rate' ? 'selected' : '' }}>Rate
                    (%)</option>
                <option value="amount" {{ isset($formula) && $formula->formula_type == 'amount' ? 'selected' : '' }}>
                    Amount</option>
                <option value="fixed" {{ isset($formula) && $formula->formula_type == 'fixed' ? 'selected' : '' }}>Fixed
                </option>
            </select>
            @error('formula_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label for="calculation_basis" class="form-label fw-medium text-dark">Calculation Basis</label>
            <select name="calculation_basis" id="calculation_basis" class="form-select" required>
                <option value="basic_pay"
                    {{ isset($formula) && $formula->calculation_basis == 'basic_pay' ? 'selected' : '' }}>Basic Pay
                </option>
                <option value="gross_pay"
                    {{ isset($formula) && $formula->calculation_basis == 'gross_pay' ? 'selected' : '' }}>Gross Pay
                </option>
                <option value="taxable_pay"
                    {{ isset($formula) && $formula->calculation_basis == 'taxable_pay' ? 'selected' : '' }}>Taxable Pay
                </option>
            </select>
            @error('calculation_basis')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label for="applies_to" class="form-label fw-medium text-dark">Applies To</label>
            <select name="applies_to" id="applies_to" class="form-select" required>
                <option value="all" {{ isset($formula) && $formula->applies_to == 'all' ? 'selected' : '' }}>All
                    Employees</option>
                <option value="specific" {{ isset($formula) && $formula->applies_to == 'specific' ? 'selected' : '' }}>
                    Specific Employees</option>
            </select>
            @error('applies_to')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="is_progressive" id="is_progressive" class="form-check-input" value="1"
                    {{ isset($formula) && $formula->is_progressive ? 'checked' : '' }}>
                <label for="is_progressive" class="form-check-label fw-medium text-dark">Progressive (Uses
                    Brackets)</label>
            </div>
        </div>
        <div class="col-12" id="minimum_amount_field"
            style="display: {{ isset($formula) && !$formula->is_progressive ? 'block' : 'none' }};">
            <label for="minimum_amount" class="form-label fw-medium text-dark">Minimum Amount</label>
            <input type="number" name="minimum_amount" id="minimum_amount" class="form-control"
                value="{{ $formula->minimum_amount ?? '' }}" step="0.01">
            @error('minimum_amount')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12" id="brackets_container"
            style="display: {{ isset($formula) && $formula->is_progressive ? 'block' : 'none' }};">
            <h5 class="fw-medium text-dark mt-3">Brackets</h5>
            <div id="brackets">
                @if(isset($formula) && $formula->brackets)
                @foreach($formula->brackets as $index => $bracket)
                @include('payroll-formulas._bracket', ['index' => $index, 'bracket' => $bracket])
                @endforeach
                @else
                @include('payroll-formulas._bracket', ['index' => 0])
                @endif
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addBracket()">Add
                Bracket</button>
        </div>
    </div>
    <div class="mt-4">
        <button type="button" class="btn btn-primary btn-modern" onclick="saveFormula(this)">
            <i class="fa fa-save me-2"></i> {{ isset($formula) ? 'Update Formula' : 'Create Formula' }}
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
                    saveFormula(form.querySelector('button[type="button"]'));
                }
                form.classList.add('was-validated');
            }, false);
        });

        $('#is_progressive').on('change', function() {
            const isProgressive = $(this).is(':checked');
            $('#brackets_container').toggle(isProgressive);
            $('#minimum_amount_field').toggle(!isProgressive);
            if (isProgressive && $('#brackets .bracket').length === 0) {
                addBracket();
            }
        });

        window.addBracket = function() {
            const index = $('#brackets .bracket').length;
            $.get('/payroll-formulas/bracket-template', {
                index: index
            }, function(template) {
                $('#brackets').append(template);
            });
        };

        window.removeBracket = function(btn) {
            $(btn).closest('.bracket').remove();
        };
    })();
</script>
@endpush