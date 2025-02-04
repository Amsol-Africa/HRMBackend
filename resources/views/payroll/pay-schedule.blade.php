<x-app-layout>
    <div class="row g-20">

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">

                    <form action="">

                        <div class="form-group mb-3">
                            <h6 class="mb-4">Select your working days in a week</h6>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="sunday" id="sunday">
                                    <label class="form-check-label" for="sunday">
                                        Sunday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="monday" id="monday" checked>
                                    <label class="form-check-label" for="monday">
                                        Monday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="tuesday" id="tuesday" checked>
                                    <label class="form-check-label" for="tuesday">
                                        Tuesday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="wednesday" id="wednesday" checked>
                                    <label class="form-check-label" for="wednesday">
                                        Wednesday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="thursday" id="thursday" checked>
                                    <label class="form-check-label" for="thursday">
                                        Thursday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="friday" id="friday" checked>
                                    <label class="form-check-label" for="friday">
                                        Friday
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="saturday" id="saturday">
                                    <label class="form-check-label" for="saturday">
                                        Saturday
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <h6 class="mb-4">Pay your employees on</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pay_schedule" id="last_working_day" value="last_working_day" checked>
                                <label class="form-check-label" for="last_working_day">
                                    Last working day of every month
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pay_schedule" id="specific_day" value="specific_day">
                                <label class="form-check-label" for="specific_day">
                                    Specific day of every month
                                </label>
                                <select class="form-select mt-2" id="day_dropdown" name="day_dropdown" style="width: 15%" disabled>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-primary"> <i class="bi bi-check-circle"></i>  Update settings </button>
                        </div>


                    </form>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/payroll-settings.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const specificDayRadio = document.getElementById('specific_day');
                const dayDropdown = document.getElementById('day_dropdown');

                specificDayRadio.addEventListener('change', function () {
                    dayDropdown.disabled = !this.checked;
                });

                document.getElementById('last_working_day').addEventListener('change', function () {
                    dayDropdown.disabled = this.checked;
                });
            });
        </script>
    @endpush

</x-app-layout>
