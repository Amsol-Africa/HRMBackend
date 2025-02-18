<x-app-layout>
    <div class="row g-20">

        <div class="col-md-">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>{{ $page }}</h5>
                    <div>
                        <a class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addOvertimeModal" href=""> <i class="bi bi-plus-square-dotted me-2"></i> Add Overtime</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('business.attendances.clock-in', $currentBusiness->slug) }}"> <i class="bi bi-calendar me-2"></i> Clock In</a>
                    </div>
                </div>
                <div class="card-body" id="overtimeContainer">
                    {{ loader() }}
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        @include('modals.add-overtime')
        <script src="{{ asset('js/main/overtime.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getOvertime()
            })
        </script>
    @endpush

</x-app-layout>
