<form id="reliefForm" class="needs-validation" novalidate>
    @csrf
    @if(isset($relief))
    <input type="hidden" name="relief_slug" value="{{ $relief->slug }}">
    @endif

    <div class="row">
        <div class="col-md-6">
            <label for="name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $relief->name ?? '' }}"
                placeholder="e.g. Personal Relief" required>
        </div>
        <div class="col-md-6">
            <label for="computation_method" class="form-label fw-medium">Computation Method <span
                    class="text-danger">*</span></label>
            <select name="computation_method" id="computation_method" class="form-select" required>
                <option value="" {{ !isset($relief) ? 'selected' : '' }}>Select</option>
                <option value="fixed" {{ isset($relief) && $relief->computation_method == 'fixed' ? 'selected' : '' }}>
                    Fixed</option>
                <option value="percentage"
                    {{ isset($relief) && $relief->computation_method == 'percentage' ? 'selected' : '' }}>Percentage
                </option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="amount" class="form-label fw-medium">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ $relief->amount ?? '' }}"
                step="0.01" placeholder="e.g. 2400">
        </div>
        <div class="col-md-6">
            <label for="percentage_of_amount" class="form-label fw-medium">Percentage Of Amount</label>
            <input type="number" name="percentage_of_amount" id="percentage_of_amount" class="form-control"
                value="{{ $relief->percentage_of_amount ?? '' }}" step="0.01" placeholder="e.g. 15">
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
        </div>
        <div class="col-md-6">
            <label for="limit" class="form-label fw-medium">Limit</label>
            <input type="number" name="limit" id="limit" class="form-control" value="{{ $relief->limit ?? '' }}"
                step="0.01" placeholder="e.g. 5000">
        </div>
        <div class="col-md-12">
            <label for="description" class="form-label fw-medium">Description</label>
            <textarea name="description" id="description" class="form-control"
                placeholder="e.g. A fixed tax relief...">{{ $relief->description ?? '' }}</textarea>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-primary" onclick="saveRelief(this)">
            <i class="fa fa-save me-2"></i> {{ isset($relief) ? 'Update Relief' : 'Create Relief' }}
        </button>
    </div>
</form>