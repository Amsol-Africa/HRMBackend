<div class="modal fade" id="allowanceModal" tabindex="-1" aria-labelledby="allowanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="allowanceModalLabel">{{ $allowance->name }} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Computation Method:</label>
                        <p>{{ ucfirst($allowance->computation_method) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Amount/Rate:</label>
                        <p>{{ $allowance->computation_method === 'fixed' ? number_format($allowance->amount, 2) : ($allowance->percentage_of_amount . '% of ' . str_replace('_', ' ', ucwords($allowance->percentage_of))) }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Taxable:</label>
                        <p>{{ $allowance->is_taxable ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="fw-medium text-dark">Description:</label>
                        <p>{{ $allowance->description ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" data-slug="{{ $allowance->slug }}"
                    onclick="editAllowance(this)">Edit</button>
            </div>
        </div>
    </div>
</div>