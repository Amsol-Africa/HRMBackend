<h5 class="mb-3">Name: <span>{{ ucfirst($payroll_formula->name) }}</span></h5>
<p><strong>Calculation Basis:</strong> <span>{{ ucfirst($payroll_formula->calculation_basis) }}</span></p>
<p><strong>Minimum Amount:</strong> <span>{{ $payroll_formula->minimum_amount }}</span></p>
<p><strong>Formula Type:</strong> <span>{{ ucfirst($payroll_formula->formula_type) }}</span></p>
<p><strong>Is Progressive:</strong> <span>{{ $payroll_formula->is_progressive ? 'Yes' : 'No' }}</span></p>
<h5 class="my-3">Formula Brackets</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Min</th>
            <th>Max</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($brackets as $bracket)
            <tr>
                <td>{{ $bracket->min }}</td>
                <td>{{ $bracket->max ?? 'No Limit' }}</td>
                <td>{{ $bracket->rate ? $bracket->rate . '%' : '' }}</td>
                <td>{{ $bracket->amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
