<x-app-layout>

    @include('locations._access_nav')

    <div class="row g-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Locations</h5>
                </div>
                <div class="card-body" id="locationsFormContainer">

                    @include('locations._form')

                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="locationsContainer">
                        <div class="card">
                            <div class="card-body"> {{ loader() }} </div>
                        </div>
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
