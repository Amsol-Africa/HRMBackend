<div>
    <h4>Manage Payroll Formulas</h4>
    <button class="btn btn-primary my-3" onclick="addFormula()">+ Add New Formula</button>

    <div id="formulas-container" class="mb-3"></div>

    <ul class="nav nav-tabs" id="payrollTabs" role="tablist">
        @foreach ($payroll_formulas as $index => $formula)
            <li class="nav-item">
                <a class="nav-link @if($index == 0) active @endif" id="formula-tab-{{ $formula->id }}" data-bs-toggle="tab" href="#formula-{{ $formula->id }}" role="tab">{{ $formula->name }}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content mt-3" id="payrollTabContent">
        @foreach ($payroll_formulas as $index => $formula)
            <div class="tab-pane fade @if($index == 0) show active @endif" id="formula-{{ $formula->id }}" role="tabpanel">
                <form id="payrollFormulasForm{{ $formula->slug }}">
                    <input type="text" name="payroll_formula_slug" hidden class="form-control" value="{{ $formula->slug }}">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $formula->name }}">
                    </div>
                    <div class="form-group">
                        <label>Calculation Basis</label>
                        <select name="calculation_basis" class="form-select">
                            <option value="basic pay" @if($formula->calculation_basis == 'basic pay') selected @endif>Basic Pay</option>
                            <option value="gross pay" @if($formula->calculation_basis == 'gross pay') selected @endif>Gross Pay</option>
                            <option value="cash pay" @if($formula->calculation_basis == 'cash pay') selected @endif>Cash Pay</option>
                            <option value="taxable pay" @if($formula->calculation_basis == 'taxable pay') selected @endif>Taxable Pay</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Minimum Amount</label>
                        <input type="number" name="minimum_amount" class="form-control" value="{{ $formula->minimum_amount }}">
                    </div>
                    <div class="form-group">
                        <label>Progressive</label>
                        <select name="is_progressive" class="form-control">
                            <option value="0" @if(!$formula->is_progressive) selected @endif>No</option>
                            <option value="1" @if($formula->is_progressive) selected @endif>Yes</option>
                        </select>
                    </div>
                    <h6 class="mb-3">Brackets</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Rate (%)</th>
                                <th>Fixed Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="brackets-{{ $formula->id }}">
                            @foreach ($formula->brackets as $bracket)
                                <tr>
                                    <td><input type="number" name="min[]" class="form-control" value="{{ $bracket->min }}"></td>
                                    <td><input type="number" name="max[]" class="form-control" value="{{ $bracket->max }}"></td>
                                    <td><input type="number" name="rate[]" class="form-control" value="{{ $bracket->rate }}"></td>
                                    <td><input type="number" name="amount[]" class="form-control" value="{{ $bracket->amount }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBracket(this)">X</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="addBracket({{ $formula->id }})">+ Add Bracket</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="savePayrollFormula(this)" data-form="payrollFormulasForm{{ $formula->slug }}"> <i class="bi bi-check-circle me-2"></i> Save Formula</button>
                </form>
            </div>
        @endforeach
    </div>
</div>

<script>
    function addFormula() {
        let formulaId = 'new_' + Date.now();
        let formulaHtml = `<div class="card mb-3 formula-card">
                <div class="card-header d-flex justify-content-between">
                    <strong>New Formula</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFormula(this)">Remove</button>
                </div>
                <div class="card-body">
                    <form id="newFormulaForm">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Calculation Basis</label>
                            <select name="calculation_basis" class="form-select">
                                <option value="basic pay">Basic Pay</option>
                                <option value="gross pay">Gross Pay</option>
                                <option value="cash pay">Cash Pay</option>
                                <option value="taxable pay">Taxable Pay</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Minimum Amount</label>
                            <input type="number" name="minimum_amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Progressive</label>
                            <select name="is_progressive" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="mt-3">
                            <h6 class="mb-3">Brackets</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Rate (%)</th>
                                        <th>Fixed Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="brackets-${formulaId}"></tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addBracket('${formulaId}')">+ Add Bracket</button>
                            <button type="button" class="btn btn-sm btn-primary" data-form="newFormulaForm" onclick="savePayrollFormula(this)"> <i class="bi bi-check-circle me-2"></i> Save Formula</button>
                        </div>
                    </form>
                </div>
            </div>`;
        document.getElementById('formulas-container').insertAdjacentHTML('beforeend', formulaHtml);
    }

    function addBracket(formulaId) {
        let bracketId = 'new_' + Date.now();
        let bracketHtml = `<tr>
                <td><input type="number" name="min[]" class="form-control"></td>
                <td><input type="number" name="max[]" class="form-control"></td>
                <td><input type="number" name="rate[]" class="form-control"></td>
                <td><input type="number" name="amount[]" class="form-control"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBracket(this)">X</button></td>
            </tr>`;
        document.getElementById('brackets-' + formulaId).insertAdjacentHTML('beforeend', bracketHtml);
    }

    function removeFormula(button) {
        button.closest('.formula-card').remove();
    }

    function removeBracket(button) {
        button.closest('tr').remove();
    }
</script>
