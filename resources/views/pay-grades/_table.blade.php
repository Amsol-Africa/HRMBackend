<div id="payGradesTable" class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
        <thead class="bg-light">
            <tr>
                <th scope="col" class="text-dark fw-semibold">Name</th>
                <th scope="col" class="text-dark fw-semibold">Amount</th>
                <th scope="col" class="text-dark fw-semibold">Job Category</th>
                <th scope="col" class="text-dark fw-semibold">Department</th>
                <th scope="col" class="text-dark fw-semibold text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payGrades as $payGrade)
            <tr>
                <td>{{ $payGrade->name }}</td>
                <td>{{ number_format($payGrade->amount, 2) }}</td>
                <td>{{ $payGrade->jobCategory ? $payGrade->jobCategory->name : 'N/A' }}</td>
                <td>{{ $payGrade->department ? $payGrade->department->name : 'N/A' }}</td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-warning me-2" data-pay-grade="{{ $payGrade->id }}"
                            onclick="editPayGrade(this)">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-pay-grade="{{ $payGrade->id }}"
                            onclick="deletePayGrade(this)">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No pay grades defined yet.
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