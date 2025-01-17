<x-app-layout>
    <div class="row g-20">

        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">

                    @include('relief._form')

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/reliefs.js') }}" type="module"></script>
        <script>
            function toggleReliefFields(value) {
                const rateField = document.querySelector('.rate-field');
                const amountField = document.querySelector('.amount-field');

                if (value === 'rate') {
                    rateField.style.display = 'block';
                    amountField.style.display = 'none';
                } else if (value === 'amount') {
                    rateField.style.display = 'none';
                    amountField.style.display = 'block';
                }
            }

            function initializeReliefFields() {
                const selectElement = document.getElementById('relief_type');
                const selectedValue = selectElement.value;
                toggleReliefFields(selectedValue);
            }

            document.addEventListener('DOMContentLoaded', () => {
                initializeReliefFields();

                const selectElement = document.getElementById('relief_type');

                selectElement.addEventListener('change', (event) => {
                    toggleReliefFields(event.target.value);
                });
            });

            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', function(e) {
                    if (this.value < 0) this.value = 0;
                });
            });
        </script>
    @endpush

</x-app-layout>
