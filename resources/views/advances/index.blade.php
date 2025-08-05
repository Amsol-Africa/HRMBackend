<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="card-header">Create a new advance</h5>
                </div>
                <div class="card-body" id="advancesFormContainer">
                    @include('advances._form')
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Advances</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive" id="advancesContainer">
                        <div class="text-center py-4">{{ loader() }}</div>
                    </div>
                </div>
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
