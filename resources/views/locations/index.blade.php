<x-app-layout>

    @include('locations._access_nav')

    <div class="row g-4">
        <!-- Form Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="card-header">Add New Location</h5>
                </div>
                <div class="card-body p-4" id="locationsFormContainer">
                    @include('locations._form')
                </div>
            </div>
        </div>

        <!-- Locations Table Card -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Locations</h5>
                    <div id="exportButtons" class="d-flex gap-2"></div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive" id="locationsContainer">
                        <div class="text-center py-4">{{ loader() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="{{ asset('js/main/locations.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getLocations()
            })
        </script>
    @endpush

</x-app-layout>
