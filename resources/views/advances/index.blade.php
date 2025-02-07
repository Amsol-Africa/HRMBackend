<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body" id="advancesFormContainer">
                    @include('advances._form')
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body" id="advancesContainer"> {{ loader() }} </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/advances.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getAdvances()
            })
        </script>

    @endpush

</x-app-layout>
