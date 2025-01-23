<x-app-layout>
    <div class="row g-20">

        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ $page }}</h5>
                </div>
                <div class="card-body">

                    @include('deductions._form')

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/deductions.js') }}" type="module"></script>
    @endpush

</x-app-layout>
