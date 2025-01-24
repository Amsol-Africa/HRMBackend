<x-app-layout>
    <div id="clientsContainer">
        <div class="card">
            <div class="card-body"> {{ loader() }} </div>
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
