<x-app-layout>
    <div class="row g-20">

        <div class="col-md-10" id="employeesFormContainer">
            @include('employees._form')
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const academicDetailsContainer = document.querySelector('.academic-details-container');
                const addButton = document.getElementById('addAcademicDetails');

                const familyMembersContainer = document.querySelector('.family-members-container');
                const addFamilyMemberButton = document.getElementById('addFamilyMemberDetails');

                // Add new academic details row
                addButton.addEventListener('click', function() {
                    // Clone the first row
                    const firstRow = academicDetailsContainer.querySelector('.academic-details-row');
                    const newRow = firstRow.cloneNode(true);

                    // Clear input values
                    newRow.querySelectorAll('input').forEach(input => input.value = '');

                    // Add remove button
                    const removeButtonCol = document.createElement('div');
                    removeButtonCol.className = 'col-md-1';
                    removeButtonCol.innerHTML = `
                        <button type="button" class="btn btn-danger remove-academic-details mt-4">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                    newRow.appendChild(removeButtonCol);

                    // Append to container
                    academicDetailsContainer.appendChild(newRow);

                    // Reinitialize datepicker if using a datepicker library
                    initDatepickers(newRow);
                });

                // Event delegation for remove buttons
                academicDetailsContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-academic-details')) {
                        const rows = academicDetailsContainer.querySelectorAll('.academic-details-row');
                        if (rows.length > 1) {
                            e.target.closest('.academic-details-row').remove();
                        } else {
                            alert('At least one academic details row must remain.');
                        }
                    }
                });


                addFamilyMemberButton.addEventListener('click', function () {
                    // Clone the first row
                    const firstRow = familyMembersContainer.querySelector('.family-members-row');
                    const newRow = firstRow.cloneNode(true);

                    // Clear input values
                    newRow.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });

                    // Add remove button
                    const removeButtonCol = document.createElement('div');
                    removeButtonCol.className = 'col-md-12 text-end mt-2';
                    removeButtonCol.innerHTML = `
                        <button type="button" class="btn btn-danger remove-family-member">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    `;
                    newRow.appendChild(removeButtonCol);

                    // Append new row to the container
                    familyMembersContainer.appendChild(newRow);

                    // Reinitialize datepicker if using a datepicker library
                    initDatepickers(newRow);
                });

                // Event delegation for remove buttons
                familyMembersContainer.addEventListener('click', function (e) {
                    if (e.target.closest('.remove-family-member')) {
                        const rows = familyMembersContainer.querySelectorAll('.family-members-row');
                        if (rows.length > 1) {
                            e.target.closest('.family-members-row').remove();
                        } else {
                            alert('At least one family member row must remain.');
                        }
                    }
                });


                function initDatepickers(row) {
                    const datepickers = row.querySelectorAll('.datepicker');
                    datepickers.forEach(picker => {
                        picker.flatpickr({
                            enableTime: true,
                            dateFormat: "Y-m-d H:i",
                            time_24hr: true,
                            allowInput: true,
                        });
                    });
                }
            });
        </script>
    @endpush

</x-app-layout>
