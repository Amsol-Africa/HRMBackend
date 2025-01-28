<x-app-layout>

    @include('clients._access_nav')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Managed Businesses / Clients</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="clientsContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/clients.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getClients()
            })
        </script>

    @endpush

</x-app-layout>
