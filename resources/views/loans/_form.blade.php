<form id="loanForm" method="post">
    @csrf
    @if(isset($loan))
        <input type="hidden" name="loan_id" value="{{ $loan->id }}">
    @endif

    <div class="form-group mb-3">
        <label for="employee_id">Employee</label>
        <select class="form-control" id="employee_id" name="employee_id" required>
            <option value="">Select an Employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ isset($loan) && $loan->employee_id == $employee->id ? 'selected' : '' }}>
                    {{ $employee->user->name }}
                </option>
            @endforeach
        </select>
    </div>

   <div class="row g-2 mb-3">
        <div class="col-md-6">
            <label for="amount">Loan Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required min="1" step="0.01" value="{{ isset($loan) ? $loan->amount : old('amount') }}">
        </div>

        <div class="col-md-6">
            <label for="interest_rate">Interest Rate (%)</label>
            <input type="number" class="form-control" id="interest_rate" name="interest_rate" min="0" step="0.01" value="{{ isset($loan) ? $loan->interest_rate : old('interest_rate') }}">
            <small class="form-text text-muted">Enter as a percentage (e.g., 5 for 5%)</small>
        </div>
   </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <label for="term_months">Loan Term (Months)</label>
            <input type="number" class="form-control" id="term_months" name="term_months" min="1" value="{{ isset($loan) ? $loan->term_months : old('term_months') }}">
        </div>

        <div class="col-md-6">
            <label for="start_date">Loan Start Date</label>
            <input type="date" class="form-control datepicker" id="start_date" name="start_date" required value="{{ isset($loan) ? $loan->start_date : old('start_date') }}">
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" class="form-control" rows="4">{{ isset($loan) ? $loan->notes : old('notes') }}</textarea>
    </div>

    <div>
        <button onclick="saveLoan(this)" type="button" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-2"></i> {{ isset($loan) ? 'Update Loan' : 'Save Loan' }}
        </button>
    </div>
</form>
