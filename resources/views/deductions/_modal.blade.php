<div class="modal fade" id="deductionModal" tabindex="-1" aria-labelledby="deductionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="deductionModalLabel">{{ $deduction->name }} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Name:</label>
                        <p>{{ $deduction->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Computation Method:</label>
                        <p>{{ ucfirst($deduction->computation_method) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Calculation Basis:</label>
                        <p>{{ ucwords(str_replace('_', ' ', $deduction->calculation_basis)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Amount/Rate/Formula:</label>
                        <p>
                            @if($deduction->computation_method === 'fixed')
                            {{ number_format($deduction->amount, 2) }}
                            @elseif($deduction->computation_method === 'rate')
                            {{ $deduction->rate }}%
                            @else
                            {{ $deduction->formula ?? 'N/A' }}
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Actual Amount:</label>
                        <p>{{ $deduction->actual_amount ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Fraction to Consider:</label>
                        <p>{{ ucwords(str_replace('_', ' ', $deduction->fraction_to_consider)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Limit:</label>
                        <p>{{ $deduction->limit ? number_format($deduction->limit, 2) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Round Off:</label>
                        <p>{{ ucwords(str_replace('_', ' ', $deduction->round_off)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Decimal Places:</label>
                        <p>{{ $deduction->decimal_places }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Description:</label>
                        <p>{{ $deduction->description ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Status:</label>
                        <p>{{ $deduction->is_optional ? 'Optional' : 'Mandatory' }}
                            ({{ $deduction->is_statutory ? 'Statutory' : 'Non-Statutory' }})</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-medium text-dark">Created By:</label>
                        <p>{{ $deduction->created_by ? \App\Models\User::find($deduction->created_by)->name : 'Unknown' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-modern" data-deduction="{{ $deduction->id }}"
                    onclick="editDeduction(this)">Edit</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.modal-content {
    border-radius: 12px;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-body label {
    font-size: 0.95rem;
    color: #495057;
}

.modal-body p {
    margin: 0;
    font-size: 1rem;
    color: #212529;
}
</style>
@endpush