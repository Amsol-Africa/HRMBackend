<div class="bracket row g-3 mb-3" data-index="{{ $index ?? 0 }}">
    <div class="col-md-3">
        <label class="form-label fw-medium text-dark">From (KES)</label>
        <input type="number" name="brackets[{{ $index ?? 0 }}][min]" class="form-control"
            value="{{ $bracket->min ?? '' }}" step="0.01">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-medium text-dark">To (KES)</label>
        <input type="number" name="brackets[{{ $index ?? 0 }}][max]" class="form-control"
            value="{{ $bracket->max ?? '' }}" step="0.01">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-medium text-dark">Rate (%)</label>
        <input type="number" name="brackets[{{ $index ?? 0 }}][rate]" class="form-control"
            value="{{ $bracket->rate ?? '' }}" step="0.01">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-medium text-dark">Fixed (KES)</label>
        <input type="number" name="brackets[{{ $index ?? 0 }}][amount]" class="form-control"
            value="{{ $bracket->amount ?? '' }}" step="0.01">
    </div>
    <div class="col-md-1 d-flex align-items-end">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeBracket(this)"><i
                class="fa fa-trash"></i></button>
    </div>
</div>