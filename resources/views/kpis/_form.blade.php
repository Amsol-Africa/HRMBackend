<form id="kpiForm" novalidate>
    @csrf
    @if(isset($kpi))
    <input type="hidden" name="kpi_id" value="{{ $kpi->id }}">
    @endif

    <div class="form-row">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $kpi->name ?? old('name') }}"
                placeholder="Enter KPI name" required>
        </div>
        <div class="form-group">
            <label for="model_type">Model Type</label>
            <select class="form-select" id="model_type" name="model_type" required>
                <option value="">Select type</option>
                @foreach($modelTypes as $key => $value)
                <option value="{{ $key }}" {{ isset($kpi) && $kpi->model_type === $key ? 'selected' : '' }}>{{ $value }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" id="description" name="description"
            placeholder="Describe the KPI">{{ $kpi->description ?? old('description') }}</textarea>
    </div>

    <div id="calculationFields" style="{{ isset($kpi) && $kpi->model_type === 'manual' ? 'display: none;' : '' }}">
        <div class="form-row">
            <div class="form-group">
                <label for="calculation_method">Calculation Method</label>
                <select class="form-select" id="calculation_method" name="calculation_method">
                    <option value="">None</option>
                    @foreach($calculationMethods as $method)
                    <option value="{{ $method }}"
                        {{ isset($kpi) && $kpi->calculation_method === $method ? 'selected' : '' }}>
                        {{ ucfirst($method) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="target_value">Target Value</label>
                <input type="number" class="form-control" id="target_value" name="target_value"
                    value="{{ $kpi->target_value ?? old('target_value') }}" placeholder="e.g., 100">
            </div>
            <div class="form-group">
                <label for="comparison_operator">Comparison Operator</label>
                <select class="form-select" id="comparison_operator" name="comparison_operator">
                    <option value="">None</option>
                    @foreach($comparisonOperators as $operator)
                    <option value="{{ $operator }}"
                        {{ isset($kpi) && $kpi->comparison_operator === $operator ? 'selected' : '' }}>{{ $operator }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="business_id">Business</label>
            <select class="form-select" id="business_id" name="business_id">
                <option value="">None</option>
                <option value="{{ $business->id }}"
                    {{ isset($kpi) && $kpi->business_id == $business->id && !$kpi->location_id && !$kpi->employee_id && !$kpi->department_id && !$kpi->job_category_id ? 'selected' : '' }}>
                    {{ $business->company_name }}
                </option>
            </select>
        </div>
        <div class="form-group">
            <label for="location_id">Location</label>
            <select class="form-select" id="location_id" name="location_id">
                <option value="">None</option>
                @foreach($locations as $location)
                <option value="{{ $location->id }}"
                    {{ isset($kpi) && $kpi->location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="department_id">Department</label>
            <select class="form-select" id="department_id" name="department_id">
                <option value="">None</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}"
                    {{ isset($kpi) && $kpi->department_id == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="job_category_id">Job Category</label>
            <select class="form-select" id="job_category_id" name="job_category_id">
                <option value="">None</option>
                @foreach($jobCategories as $jobCategory)
                <option value="{{ $jobCategory->id }}"
                    {{ isset($kpi) && $kpi->job_category_id == $jobCategory->id ? 'selected' : '' }}>
                    {{ $jobCategory->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select class="form-select" id="employee_id" name="employee_id">
                <option value="">None</option>
                @foreach($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ isset($kpi) && $kpi->employee_id == $employee->id ? 'selected' : '' }}>
                    {{ $employee->user->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary btn-modern" id="kpiSubmitBtn" disabled>
            {{ isset($kpi) ? 'Update KPI' : 'Create KPI' }}
        </button>
        @if(isset($kpi))
        <button type="button" class="btn btn-secondary btn-modern" id="cancelKpiBtn">Cancel</button>
        @endif
    </div>
</form>