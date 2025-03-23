<div id="formulasTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Name</th>
                <th scope="col" class="text-dark fw-semibold">Type</th>
                <th scope="col" class="text-dark fw-semibold">Basis</th>
                <th scope="col" class="text-dark fw-semibold">Progressive</th>
                <th scope="col" class="text-dark fw-semibold">Minimum Amount</th>
                <th scope="col" class="text-dark fw-semibold">Applies To</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($formulas as $formula)
            <tr>
                <td>{{ $formula->name }}</td>
                <td>{{ ucfirst($formula->formula_type) }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $formula->calculation_basis)) }}</td>
                <td>{{ $formula->is_progressive ? 'Yes' : 'No' }}</td>
                <td>{{ is_null($formula->minimum_amount) || $formula->minimum_amount == 0 ? 'N/A' : $formula->minimum_amount }}
                </td>
                <td>{{ ucfirst($formula->applies_to) }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-warning me-2" data-formula="{{ $formula->id }}"
                            onclick="editFormula(this)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-formula="{{ $formula->id }}"
                            onclick="deleteFormula(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No payroll formulas defined yet.
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