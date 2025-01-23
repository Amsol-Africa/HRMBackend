<x-app-layout>
    <div class="row g-20">

        <div class="col-md-8">
            <div class="row" id="leaveContainer">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getLeave()
            })
        </script>

    @endpush

</x-app-layout>
