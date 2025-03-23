<x-app-layout>
    <div class="row g-20">

        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">

                    @include('payroll._payroll_form')

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/formula.js') }}" type="module"></script>
        <script>
            function toggleFormulaFields(type) {
                const rateFields = document.querySelectorAll('[name*="[rate]"]');
                const amountFields = document.querySelectorAll('[name*="[amount]"]');

                rateFields.forEach(function(field) {
                    field.style.display = (type === 'rate') ? 'block' : 'none';
                    if (field.type === 'hidden') {
                        field.type = 'text';
                    }
                });

                amountFields.forEach(function(field) {
                    field.style.display = (type === 'amount') ? 'block' : 'none';
                    if (field.type === 'hidden') {
                        field.type = 'text';
                    }
                });
            }

            function toggleBracketsSection(value) {
                const section = document.getElementById('bracketsSection');
                section.style.display = (value === 'yes') ? 'block' : 'none';
            }

            function initializeToggleFormulaFields() {
                const selectElement = document.getElementById('formula_type');
                const selectedValue = selectElement.value;
                toggleFormulaFields(selectedValue);
            }

            function initializeBracketsSection() {
                const selectElement = document.getElementById('is_progressive');
                const selectedValue = selectElement.value;
                toggleBracketsSection(selectedValue);
            }

            let bracketIndex = 1;

            function addBracket() {
                const container = document.getElementById('bracketsContainer');
                const selectElement = document.getElementById('formula_type');
                const formulaType = selectElement.value;

                const row = document.createElement('div');
                row.className = 'row g-3 mt-2';

                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" name="brackets[${bracketIndex}][min]" class="form-control" placeholder="Min Amount">
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="brackets[${bracketIndex}][max]" class="form-control" placeholder="Max Amount">
                    </div>

                    <div class="col-md-4">
                        ${formulaType === 'amount' ? `
                            <input type="text" name="brackets[${bracketIndex}][amount]" class="form-control" placeholder="Fixed Amount">
                            <input type="hidden" name="brackets[${bracketIndex}][rate]" class="form-control" placeholder="Rate (%)">
                        ` : `
                            <input type="text" name="brackets[${bracketIndex}][rate]" class="form-control" placeholder="Rate (%)">
                            <input type="hidden" name="brackets[${bracketIndex}][amount]" class="form-control" placeholder="Fixed Amount">
                        `}
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger" onclick="removeBracket(this)">Remove</button>
                    </div>
                `;

                container.appendChild(row);
                bracketIndex++;

                toggleFormulaFields(formulaType);
            }

            function removeBracket(button) {
                const row = button.closest('.row');
                row.remove();
                bracketIndex--;
            }

            document.addEventListener('DOMContentLoaded', function() {
                initializeBracketsSection();
                initializeToggleFormulaFields();

                const formulaTypeSelect = document.getElementById('formula_type');
                const progressiveSelect = document.getElementById('is_progressive');

                formulaTypeSelect.addEventListener('change', function(event) {
                    toggleFormulaFields(event.target.value);
                });

                progressiveSelect.addEventListener('change', function(event) {
                    toggleBracketsSection(event.target.value);
                });
            });
        </script>

    @endpush

</x-app-layout>
