<div id="deductionsTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Name</th>
                <th scope="col" class="text-dark fw-semibold">Method</th>
                <th scope="col" class="text-dark fw-semibold">Basis</th>
                <th scope="col" class="text-dark fw-semibold">Amount/Rate/Formula</th>
                <th scope="col" class="text-dark fw-semibold">Actual Amount</th>
                <th scope="col" class="text-dark fw-semibold">Fraction</th>
                <th scope="col" class="text-dark fw-semibold">Limit</th>
                <th scope="col" class="text-dark fw-semibold">Round Off</th>
                <th scope="col" class="text-dark fw-semibold">Decimals</th>
                <th scope="col" class="text-dark fw-semibold">Description</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($deductions as $deduction)
            <tr>
                <td>{{ $deduction->name }}</td>
                <td>{{ ucfirst($deduction->computation_method) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $deduction->calculation_basis)) }}</td>
                <td>
                    @if($deduction->computation_method === 'fixed')
                    {{ number_format($deduction->amount, 2) }}
                    @elseif($deduction->computation_method === 'rate')
                    {{ $deduction->rate }}%
                    @else
                    {{ $deduction->formula ?? 'N/A' }}
                    @endif
                </td>
                <td>{{ $deduction->actual_amount ? 'Yes' : 'No' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $deduction->fraction_to_consider)) }}</td>
                <td>{{ $deduction->limit ? number_format($deduction->limit, 2) : 'N/A' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $deduction->round_off)) }}</td>
                <td>{{ $deduction->decimal_places }}</td>
                <td>{{ $deduction->description ?? '-' }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-warning me-2" data-deduction="{{ $deduction->id }}"
                            onclick="editDeduction(this)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-deduction="{{ $deduction->id }}"
                            onclick="deleteDeduction(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No deductions defined yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>