<x-app-layout>
    <div class="row g-20">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <div>
                        <a class="btn btn-primary btn-sm" href="{{ route('business.attendances.clock-in', $currentBusiness->slug) }}"> <i class="bi bi-calendar me-2"></i> Clock In</a>
                    </div>
                </div>
                <div class="card-body" id="jobApplicationsContainer">
                    @include('attendances._clock_out_form')
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/attendances.js') }}" type="module"></script>
        <script>
            function toggleClockFields() {
                let isAbsent = document.getElementById("is_absent").checked;
                document.getElementById("clock_in").disabled = isAbsent;
                document.getElementById("clock_out").disabled = isAbsent;
            }
        </script>
    @endpush

</x-app-layout>
