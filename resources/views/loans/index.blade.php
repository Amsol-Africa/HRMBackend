<x-app-layout>

    <div class="row g-20 mb-3">

        <div class="col-md-7">
            <div class="card">
                <div class="card-body" id="loansFormContainer">
                    @include('loans._form')
                </div>
            </div>
        </div>
    </div>

    <div class="row g-20">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body" id="loansContainer"> {{ loader() }} </div>
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
