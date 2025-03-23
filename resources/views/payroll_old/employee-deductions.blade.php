<x-app-layout>
    <div class="row g-20">

        <div class="col-md-12">

            <div class="card">
                <div class="card-body mb-0" id="employeeDeductionsFormContainer">
                    {{ loader() }}
                </div>
            </div>

        </div>

    </div>

    @push('scripts')

        @include('modals.add-deductions')

        <script src="{{ asset('js/main/deductions.js') }}" type="module"></script>
        <script src="{{ asset('js/main/employee-deductions.js') }}" type="module"></script>

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                loadEmployeeDeductions()

            });

            function addDeduction() {
                let rowId = 'new_' + Date.now();
                let deductionHtml = `
                    <tr id="${rowId}">
                        <td>
                            <select class="form-select">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select">
                                @foreach ($deductions as $deduction)
                                    <option value="{{ $deduction->id }}">{{ $deduction->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" class="form-control"></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" onclick="initSaveDeduction('${rowId}')">Save</button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeDeduction('${rowId}')">X</button>
                        </td>
                    </tr>`;
                document.getElementById('deductionsTable').insertAdjacentHTML('beforeend', deductionHtml);
            }

            function editDeduction(rowId) {
                let row = document.getElementById('row_' + rowId);
                let inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => input.removeAttribute('disabled'));

                row.querySelector('.btn-warning').classList.add('d-none'); // Hide Edit button
                row.querySelector('.btn-success').classList.remove('d-none'); // Show Save button
            }

            function initSaveDeduction(rowId) {
                let row = document.getElementById(rowId.startsWith('new_') ? rowId : 'row_' + rowId);
                let employeeId = row.querySelector('td:nth-child(1) select').value;
                let deductionId = row.querySelector('td:nth-child(2) select').value;
                let amount = row.querySelector('td:nth-child(3) input').value;
                // let date = row.querySelector('td:nth-child(4) input').value;

                let data = {
                    employee_id: employeeId,
                    deduction_id: deductionId,
                    amount: amount,
                };

                saveEmployeeDeduction(data)
            }

            function removeDeduction(rowId) {
                let row = document.getElementById(rowId.startsWith('new_') ? rowId : 'row_' + rowId);

                if (rowId.startsWith('new_')) {
                    row.remove();
                    return;
                }

                if (!confirm("Are you sure you want to remove this deduction?")) return;

                const data = { deduction:rowId }

                deleteEmployeeDeduction(data, row)
            }

        </script>

    @endpush

</x-app-layout>
