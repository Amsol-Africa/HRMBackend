<div>
    <h4>Manage Payroll Formulas</h4>
    <button class="btn btn-primary my-3" onclick="addFormula()">+ Add New Formula</button>

    <div class="tab-content mt-3" id="payrollTabContent">
        @foreach ($payroll_formulas as $index => $formula)
        <div class="tab-pane fade @if($index == 0) show active @endif" id="formula-{{ $formula->id }}">
            <form id="payrollFormulasForm{{ $formula->slug }}">
                <input type="hidden" name="payroll_formula_slug" value="{{ $formula->slug }}">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $formula->name }}">
                </div>
                <div class="form-group">
                    <label>Formula Type</label>
                    <select name="formula_type" class="form-select">
                        <option value="rate" @if($formula->formula_type == 'rate') selected @endif>Rate (%)</option>
                        <option value="amount" @if($formula->formula_type == 'amount') selected @endif>Fixed Amount
                        </option>
                        <option value="fixed" @if($formula->formula_type == 'fixed') selected @endif>Fixed Value
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Calculation Basis</label>
                    <select name="calculation_basis" class="form-select">
                        <option value="basic_pay" @if($formula->calculation_basis == 'basic_pay') selected @endif>Basic
                            Pay</option>
                        <option value="gross_pay" @if($formula->calculation_basis == 'gross_pay') selected @endif>Gross
                            Pay</option>
                        <option value="taxable_pay" @if($formula->calculation_basis == 'taxable_pay') selected
                            @endif>Taxable Pay</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Applies To</label>
                    <select name="applies_to" class="form-select">
                        <option value="all" @if($formula->applies_to == 'all') selected @endif>All Employees</option>
                        <option value="specific" @if($formula->applies_to == 'specific') selected @endif>Specific
                            Employees</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Progressive</label>
                    <select name="is_progressive" class="form-select"
                        onchange="toggleBrackets(this, '{{ $formula->id }}')">
                        <option value="0" @if(!$formula->is_progressive) selected @endif>No</option>
                        <option value="1" @if($formula->is_progressive) selected @endif>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Minimum Amount</label>
                    <input type="number" name="minimum_amount" class="form-control"
                        value="{{ $formula->minimum_amount }}">
                </div>
                <div id="brackets-{{ $formula->id }}" class="@if(!$formula->is_progressive) d-none @endif">
                    <h6>Brackets</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Rate (%)</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="brackets-body-{{ $formula->id }}">
                            @foreach ($formula->brackets as $bracket)
                            <tr>
                                <td><input type="number" name="min[]" class="form-control" value="{{ $bracket->min }}">
                                </td>
                                <td><input type="number" name="max[]" class="form-control" value="{{ $bracket->max }}">
                                </td>
                                <td><input type="number" name="rate[]" class="form-control"
                                        value="{{ $bracket->rate }}"></td>
                                <td><input type="number" name="amount[]" class="form-control"
                                        value="{{ $bracket->amount }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger"
                                        onclick="removeBracket(this)">X</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="addBracket('{{ $formula->id }}')">+
                        Add Bracket</button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="savePayrollFormula(this)"
                    data-form="payrollFormulasForm{{ $formula->slug }}">Save Formula</button>
            </form>
        </div>
        @endforeach
    </div>
</div>

<script>
function addFormula() {
    let formulaId = 'new_' + Date.now();
    let formulaHtml = `<div class="card mb-3">
            <div class="card-body">
                <form id="newFormulaForm_${formulaId}">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Formula Type</label>
                        <select name="formula_type" class="form-select">
                            <option value="rate">Rate (%)</option>
                            <option value="amount">Fixed Amount</option>
                            <option value="fixed">Fixed Value</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Calculation Basis</label>
                        <select name="calculation_basis" class="form-select">
                            <option value="basic_pay">Basic Pay</option>
                            <option value="gross_pay">Gross Pay</option>
                            <option value="taxable_pay">Taxable Pay</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Applies To</label>
                        <select name="applies_to" class="form-select">
                            <option value="all">All Employees</option>
                            <option value="specific">Specific Employees</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Progressive</label>
                        <select name="is_progressive" class="form-select" onchange="toggleBrackets(this, '${formulaId}')">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Minimum Amount</label>
                        <input type="number" name="minimum_amount" class="form-control">
                    </div>
                    <div id="brackets-${formulaId}" class="d-none">
                        <h6>Brackets</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Rate (%)</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="brackets-body-${formulaId}"></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addBracket('${formulaId}')">+ Add Bracket</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" data-form="newFormulaForm_${formulaId}" onclick="savePayrollFormula(this)">Save Formula</button>
                </form>
            </div>
        </div>`;
    document.getElementById('payrollTabContent').insertAdjacentHTML('beforeend', formulaHtml);
}

function addBracket(formulaId) {
    let bracketHtml = `<tr>
            <td><input type="number" name="min[]" class="form-control"></td>
            <td><input type="number" name="max[]" class="form-control"></td>
            <td><input type="number" name="rate[]" class="form-control"></td>
            <td><input type="number" name="amount[]" class="form-control"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBracket(this)">X</button></td>
        </tr>`;
    document.getElementById(`brackets-body-${formulaId}`).insertAdjacentHTML('beforeend', bracketHtml);
}

function removeBracket(button) {
    button.closest('tr').remove();
}

function toggleBrackets(select, formulaId) {
    document.getElementById(`brackets-${formulaId}`).classList.toggle('d-none', select.value == '0');
}
</script>