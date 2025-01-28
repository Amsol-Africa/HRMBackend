<x-app-layout>


    <card>
        <div class="card-body">
            {{ loader() }}
        </div>
    </card>

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
