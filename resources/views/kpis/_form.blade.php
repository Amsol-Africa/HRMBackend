<form method="POST" action="{{ isset($kpi) ? route('kpis.update', $kpi->slug) : route('kpis.store') }}">
    @csrf
    @if(isset($kpi)) @method('PUT') @endif

    <div>
        <label for="name">Name</label>
        <input type="text" name="name" value="{{ old('name', $kpi->name ?? '') }}" required>
    </div>
    <div>
        <label for="slug">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $kpi->slug ?? '') }}"
            {{ isset($kpi) ? 'disabled' : 'required' }}>
    </div>
    <div>
        <label for="model_type">Model Type</label>
        <select name="model_type" required>
            @foreach ($modelTypes as $value => $label)
            <option value="{{ $value }}" {{ old('model_type', $kpi->model_type ?? '') == $value ? 'selected' : '' }}>
                {{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="description">Description</label>
        <textarea name="description">{{ old('description', $kpi->description ?? '') }}</textarea>
    </div>
    <div>
        <label for="calculation_method">Calculation Method</label>
        <select name="calculation_method" required>
            @foreach ($calculationMethods as $method)
            <option value="{{ $method }}"
                {{ old('calculation_method', $kpi->calculation_method ?? '') == $method ? 'selected' : '' }}>
                {{ ucfirst($method) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="target_value">Target Value</label>
        <input type="number" name="target_value" value="{{ old('target_value', $kpi->target_value ?? '') }}" required>
    </div>
    <div>
        <label for="comparison_operator">Comparison Operator</label>
        <select name="comparison_operator" required>
            @foreach ($comparisonOperators as $operator)
            <option value="{{ $operator }}"
                {{ old('comparison_operator', $kpi->comparison_operator ?? '') == $operator ? 'selected' : '' }}>
                {{ $operator }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">{{ isset($kpi) ? 'Update' : 'Create' }} KPI</button>
</form>