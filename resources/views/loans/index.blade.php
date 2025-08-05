<x-app-layout>

    <div class="row g-20 mb-3">

        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="card-header">Create a new loan</h5>
                </div>
                <div class="card-body" id="loansFormContainer">
                    @include('loans._form')
                </div>
            </div>
        </div>
    </div>

    <div class="row g-20">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Loans</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive" id="loansContainer">
                        <div class="text-center py-4">{{ loader() }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/loans.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getLoans()
            })
        </script>
    @endpush

</x-app-layout>
