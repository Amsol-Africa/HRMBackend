<table class="table table-striped table-bordered" style="width: 100%" id="payrollFormulas">
    <thead>
        <tr>
            <th>#</th>
            <th>Formula Name</th>
            <th>Calculation Basis</th>
            <th>Minimum Amount</th>
            <th>Is Progressive</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payroll_formulas as $key => $formula)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $formula->name }}</td>
                <td>{{ ucfirst($formula->calculation_basis) }}</td>
                <td>{{ $formula->minimum_amount }}</td>
                <td>{{ $formula->is_progressive ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="" class="btn btn-primary btn-sm"> <i class="bi bi-pencil-square"></i> Edit</a>
                    <button type="button"  class="btn btn-danger btn-sm" data-payroll-formula="{{ $formula->slug }}" onclick="deletePayrollFormula(this)""> <i class="bi bi-trash"></i> Delete</button>
                    <button type="button" class="btn btn-success btn-sm" data-payroll-formula="{{ $formula->slug }}" onclick="showFormula(this)" class="view-btn"> <i class="bi bi-view-list"></i> View</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
