<div id="reliefsTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Name</th>
                <th scope="col" class="text-dark fw-semibold">Computation Method</th>
                <th scope="col" class="text-dark fw-semibold">Amount</th>
                <th scope="col" class="text-dark fw-semibold">Percentage Of</th>
                <th scope="col" class="text-dark fw-semibold">Limit</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reliefs as $relief)
            <tr>
                <td>{{ $relief->name }}</td>
                <td>{{ ucfirst($relief->computation_method) }}</td>
                <td>{{ $relief->amount ? number_format($relief->amount, 2) : 'N/A' }}</td>
                <td>{{ $relief->percentage_of_amount ? $relief->percentage_of_amount . '% of ' . str_replace('_', ' ', ucwords($relief->percentage_of)) : 'N/A' }}
                </td>
                <td>{{ $relief->limit ? number_format($relief->limit, 2) : 'N/A' }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info me-2" data-slug="{{ $relief->slug }}"
                            onclick="viewRelief(this)">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-2" data-slug="{{ $relief->slug }}"
                            onclick="editRelief(this)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-slug="{{ $relief->slug }}"
                            onclick="deleteRelief(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No reliefs defined yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>