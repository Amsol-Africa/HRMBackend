<x-app-layout>


    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    @include('leave._request_leave_form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getLeave('pending');
                $('#myTab button').on('click', function (event) {
                    event.preventDefault();
                    $(this).tab('show');
                    const status = $(this).attr('aria-controls');
                    getLeave(1, status)
                });
            });
        </script>

    @endpush

</x-app-layout>
