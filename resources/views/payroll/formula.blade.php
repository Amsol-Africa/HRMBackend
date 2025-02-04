<x-app-layout>



    <div class="row g-3">
        <div class="col-md-12">
            <ul class="nav nav-tabs mb-3" id="deductionsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="nhif-tab" data-bs-toggle="tab" data-bs-target="#nhif" type="button" role="tab" aria-controls="nhif" aria-selected="true">
                        NHIF
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nssf-tab" data-bs-toggle="tab" data-bs-target="#nssf" type="button" role="tab" aria-controls="nssf" aria-selected="false">
                        NSSF
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="housing-tab" data-bs-toggle="tab" data-bs-target="#housing" type="button" role="tab" aria-controls="housing" aria-selected="false">
                        Housing Levy
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="deductionsTabContent">
                <!-- NHIF -->
                <div class="tab-pane fade show active" id="nhif" role="tabpanel" aria-labelledby="nhif-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="bi bi-hospital"></i> NHIF Deduction</h5>
                            <label for="nhif_fixed" class="form-label">Fixed NHIF Rate</label>
                            <input type="number" class="form-control" id="nhif_fixed" name="nhif_fixed" placeholder="Enter NHIF amount">

                            <h6 class="mt-3">Bracket-Based NHIF (Optional)</h6>
                            <div id="nhif-brackets">
                                <button type="button" class="btn btn-sm btn-primary mb-2" id="add-nhif-bracket">+ Add Bracket</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NSSF -->
                <div class="tab-pane fade" id="nssf" role="tabpanel" aria-labelledby="nssf-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="bi bi-piggy-bank"></i> NSSF Deduction</h5>
                            <label for="nssf_tier" class="form-label">NSSF Tier</label>
                            <select class="form-control" id="nssf_tier" name="nssf_tier">
                                <option value="tier1">Tier 1</option>
                                <option value="tier2">Tier 2</option>
                            </select>
                            <label for="nssf_rate" class="form-label mt-2">NSSF Rate (%)</label>
                            <input type="number" class="form-control" id="nssf_rate" name="nssf_rate" placeholder="Enter percentage">
                        </div>
                    </div>
                </div>

                <!-- Housing Levy -->
                <div class="tab-pane fade" id="housing" role="tabpanel" aria-labelledby="housing-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="bi bi-house-door"></i> Housing Levy</h5>
                            <label for="housing_rate" class="form-label">Housing Levy Rate (%)</label>
                            <input type="number" class="form-control" id="housing_rate" name="housing_rate" placeholder="Enter percentage">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('modals.payroll-formula')
        <script src="{{ asset('js/main/formula.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                getPayrollFormulas('nhif')

                // NHIF Bracket Handling
                const nhifBracketsContainer = document.getElementById('nhif-brackets');
                const addNhifBracketBtn = document.getElementById('add-nhif-bracket');

                addNhifBracketBtn.addEventListener('click', function () {
                    const bracketIndex = document.querySelectorAll('.nhif-bracket').length;
                    const bracketHtml = `
                        <div class="nhif-bracket mb-2">
                            <label class="form-label">Bracket ${bracketIndex + 1} - Salary Range</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="nhif_brackets[${bracketIndex}][min]" placeholder="Min Salary">
                                <input type="number" class="form-control" name="nhif_brackets[${bracketIndex}][max]" placeholder="Max Salary">
                                <input type="number" class="form-control" name="nhif_brackets[${bracketIndex}][amount]" placeholder="Deduction Amount">
                                <button type="button" class="btn btn-danger remove-nhif-bracket">X</button>
                            </div>
                        </div>`;

                    nhifBracketsContainer.insertAdjacentHTML('beforeend', bracketHtml);
                });

                nhifBracketsContainer.addEventListener('click', function (event) {
                    if (event.target.classList.contains('remove-nhif-bracket')) {
                        event.target.closest('.nhif-bracket').remove();
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>
