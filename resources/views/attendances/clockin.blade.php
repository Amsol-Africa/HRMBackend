<x-app-layout>
    <div class="row g-20">

        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <div>
                        <a class="btn btn-primary btn-sm" href="{{ route('business.attendances.clock-out', $currentBusiness->slug) }}"> <i class="bi bi-calendar me-2"></i> Clock Out</a>
                    </div>
                </div>
                <div class="card-body" id="jobApplicationsContainer">

                    @include('attendances._clock_in_form')

                </div>
            </div>
        </div>

        <div class="col-md-7" id="clockinsContainer">

        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/attendances.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getClockins();
            });
        </script>
    @endpush

</x-app-layout>
