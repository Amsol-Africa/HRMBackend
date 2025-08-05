<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" id="leavePeriodsForm">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Leave Period Name" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" placeholder="Start Date" class="form-control datepicker" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" placeholder="End Date" class="form-control datepicker" required>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="accept_applications" name="accept_applications" checked value="1">
                                <label class="form-check-label" for="accept_applications">Accept Applications</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="can_accrue" name="can_accrue" checked value="1">
                                <label class="form-check-label" for="can_accrue">Can Accrue</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="restrict_applications_within_dates" name="restrict_applications_within_dates" value="1">
                                <label class="form-check-label" for="restrict_applications_within_dates">Restrict Applications Within Dates</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="autocreate" name="autocreate" value="1">
                                <label class="form-check-label" for="autocreate">Autocreate Next Period</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" onclick="saveLeavePeriods(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle"></i> Save Leave Period </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div id="leavePeriodsContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        @include('modals.leave-periods')
        <script src="{{ asset('js/main/leave-periods.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getLeavePeriods()
            })
        </script>

    @endpush

</x-app-layout>
