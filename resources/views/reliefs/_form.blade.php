<form id="reliefForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($relief))
    <input type="hidden" name="relief_id" value="{{ $relief->id }}">
    @endif

    <div class="row">
        <div class="col-md-6">
            <label for="name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $relief->name ?? '' }}"
                placeholder="e.g. Personal Relief" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="type" class="form-label fw-medium">Type <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select" required>
                <option value="" {{ !isset($relief) ? 'selected' : '' }}>Select</option>
                <option value="deductible_before_tax"
                    {{ isset($relief) && $relief->type == 'deductible_before_tax' ? 'selected' : '' }}>Deductible before
                    tax</option>
                <option value="deductible_after_tax"
                    {{ isset($relief) && $relief->type == 'deductible_after_tax' ? 'selected' : '' }}>Deductible after
                    tax</option>
            </select>
            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="greatest_or_least_of" class="form-label fw-medium">Greatest or Least Of <span
                    class="text-danger">*</span></label>
            <select name="greatest_or_least_of" id="greatest_or_least_of" class="form-select" required>
                <option value="" {{ !isset($relief) ? 'selected' : '' }}>Select</option>
                <option value="greatest"
                    {{ isset($relief) && $relief->greatest_or_least_of == 'greatest' ? 'selected' : '' }}>Greatest
                </option>
                <option value="least"
                    {{ isset($relief) && $relief->greatest_or_least_of == 'least' ? 'selected' : '' }}>Least</option>
            </select>
            @error('greatest_or_least_of') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="fixed_amount" class="form-label fw-medium">Fixed Amount</label>
            <input type="number" name="fixed_amount" id="fixed_amount" class="form-control"
                value="{{ $relief->amount ?? '' }}" step="0.01" placeholder="e.g. 5000">
            @error('fixed_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-check mt-2">
                <input type="checkbox" name="actual_amount" id="actual_amount" class="form-check-input" value="1"
                    {{ isset($relief) && $relief->actual_amount ? 'checked' : '' }}>
                <label for="actual_amount" class="form-check-label">Actual Amount</label>
            </div>
        </div>

        <div class="col-md-6">
            <label for="percentage_of_amount" class="form-label fw-medium">Percentage Of this Amount</label>
            <input type="number" name="percentage_of_amount" id="percentage_of_amount" class="form-control"
                value="{{ $relief->percentage_of_amount ?? '' }}" step="0.01" placeholder="e.g. 30">
            @error('percentage_of_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="percentage_of" class="form-label fw-medium">Percentage Of</label>
            <select name="percentage_of" id="percentage_of" class="form-select">
                <option value="" {{ !isset($relief) || !$relief->percentage_of ? 'selected' : '' }}>Select</option>
                <option value="total_salary"
                    {{ isset($relief) && $relief->percentage_of == 'total_salary' ? 'selected' : '' }}>Total Salary
                </option>
                <option value="basic_salary"
                    {{ isset($relief) && $relief->percentage_of == 'basic_salary' ? 'selected' : '' }}>Basic Salary
                </option>
                <option value="net_salary"
                    {{ isset($relief) && $relief->percentage_of == 'net_salary' ? 'selected' : '' }}>Net Salary</option>
            </select>
            @error('percentage_of') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="fraction_to_consider" class="form-label fw-medium">Fraction to Consider <span
                    class="text-danger">*</span></label>
            <select name="fraction_to_consider" id="fraction_to_consider" class="form-select" required>
                <option value="" {{ !isset($relief) ? 'selected' : '' }}>Select</option>
                <option value="employee_only"
                    {{ isset($relief) && $relief->fraction_to_consider == 'employee_only' ? 'selected' : '' }}>Employee
                    only</option>
                <option value="employee_and_employer"
                    {{ isset($relief) && $relief->fraction_to_consider == 'employee_and_employer' ? 'selected' : '' }}>
                    Employee & Employer</option>
            </select>
            @error('fraction_to_consider') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="limit" class="form-label fw-medium">Limit</label>
            <input type="number" name="limit" id="limit" class="form-control" value="{{ $relief->limit ?? '' }}"
                step="0.01" placeholder="e.g. 12500">
            @error('limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="round_off" class="form-label fw-medium">Round Off <span class="text-danger">*</span></label>
            <select name="round_off" id="round_off" class="form-select" required>
                <option value="" {{ !isset($relief) ? 'selected' : '' }}>Select</option>
                <option value="round_off_up"
                    {{ isset($relief) && $relief->round_off == 'round_off_up' ? 'selected' : '' }}>Round up</option>
                <option value="round_off_down"
                    {{ isset($relief) && $relief->round_off == 'round_off_down' ? 'selected' : '' }}>Round down</option>
            </select>
            @error('round_off') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label for="decimal_places" class="form-label fw-medium">Decimal Places <span
                    class="text-danger">*</span></label>
            <select name="decimal_places" id="decimal_places" class="form-select" required>
                @for ($i = 0; $i <= 5; $i++) <option value="{{ $i }}"
                    {{ isset($relief) && $relief->decimal_places == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
            </select>
            @error('decimal_places') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary" onclick="saveRelief(this)">
            <i class="fa fa-save me-2"></i> {{ isset($relief) ? 'Update Relief' : 'Create Relief' }}
        </button>
    </div>
</form>