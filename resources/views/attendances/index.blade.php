<x-app-layout>

    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>{{ $page }}</h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="row">
                                <div class="col-md-7">
                                    <form action="">
                                        <input type="text" class="form-control datepicker" name="date" id="date" value="{{ now()->format("Y-m-d H:i") }}">
                                    </form>
                                </div>
                                <div class="col-md-5">
                                    <a class="btn btn-primary btn-sm" href="{{ route('business.attendances.clock-in', $currentBusiness->slug) }}"> <i class="bi bi-calendar-check me-2"></i> Record Attendances</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-body" id="attendancesContainer">
                    {{ loader() }}
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/attendances.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                let date = $('#date').val();
                getAttendances(date);

                $('#date').on('change', function () {
                    let selectedDate = $(this).val();
                    getAttendances(selectedDate);
                });
            });
        </script>
    @endpush

</x-app-layout>
