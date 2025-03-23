<div id="reliefsTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Name</th>
                <th scope="col" class="text-dark fw-semibold">Type</th>
                <th scope="col" class="text-dark fw-semibold">Greatest or Least Of</th>
                <th scope="col" class="text-dark fw-semibold">Fixed Amount</th>
                <th scope="col" class="text-dark fw-semibold">Percentage Of</th>
                <th scope="col" class="text-dark fw-semibold">Fraction to Consider</th>
                <th scope="col" class="text-dark fw-semibold">Limit</th>
                <th scope="col" class="text-dark fw-semibold">Round Off</th>
                <th scope="col" class="text-dark fw-semibold">Decimal Places</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reliefs as $relief)
            <tr>
                <td>{{ $relief->name }}</td>
                <td>{{ str_replace('_', ' ', ucwords($relief->type)) }}</td>
                <td>{{ ucfirst($relief->greatest_or_least_of) }}</td>
                <td>{{ $relief->amount ? number_format($relief->amount, 2) : 'N/A' }}</td>
                <td>{{ $relief->percentage_of_amount ? $relief->percentage_of_amount . '% of ' . str_replace('_', ' ', ucwords($relief->percentage_of)) : 'N/A' }}
                </td>
                <td>{{ str_replace('_', ' ', ucwords($relief->fraction_to_consider)) }}</td>
                <td>{{ $relief->limit ? number_format($relief->limit, 2) : 'N/A' }}</td>
                <td>{{ str_replace('_', ' ', ucwords($relief->round_off)) }}</td>
                <td>{{ $relief->decimal_places }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info me-2" data-relief="{{ $relief->id }}"
                            onclick="viewRelief(this)">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-2" data-relief="{{ $relief->id }}"
                            onclick="editRelief(this)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-relief="{{ $relief->id }}"
                            onclick="deleteRelief(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No reliefs defined yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('styles')
<style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table th,
.table td {
    vertical-align: middle;
}

.btn-group .btn {
    padding: 6px 12px;
}

@media (max-width: 576px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .btn-group .btn {
        width: 100%;
        text-align: center;
    }
}
</style>
@endpush