<div class="modal fade" id="reliefModal" tabindex="-1" aria-labelledby="reliefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="reliefModalLabel">{{ $relief->name }} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Type:</label>
                        <p>{{ str_replace('_', ' ', ucwords($relief->type)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Greatest or Least Of:</label>
                        <p>{{ ucfirst($relief->greatest_or_least_of) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Fixed Amount:</label>
                        <p>{{ $relief->amount ? number_format($relief->amount, 2) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Actual Amount:</label>
                        <p>{{ $relief->actual_amount ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Percentage Of:</label>
                        <p>{{ $relief->percentage_of_amount ? $relief->percentage_of_amount . '% of ' . str_replace('_', ' ', ucwords($relief->percentage_of)) : 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Fraction to Consider:</label>
                        <p>{{ str_replace('_', ' ', ucwords($relief->fraction_to_consider)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Limit:</label>
                        <p>{{ $relief->limit ? number_format($relief->limit, 2) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Round Off:</label>
                        <p>{{ str_replace('_', ' ', ucwords($relief->round_off)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Decimal Places:</label>
                        <p>{{ $relief->decimal_places }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Status:</label>
                        <p>{{ $relief->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-modern" data-relief="{{ $relief->id }}"
                    onclick="editRelief(this)">Edit</button>
            </div>
        </div>
    </div>
</div>