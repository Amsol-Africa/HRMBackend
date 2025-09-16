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
                // Initial load for "pending" (adjust according to your leave.js)
                if (typeof getLeave === 'function') {
                    getLeave('pending');
                }

                // Example for tab handling if you have nav tabs with IDs
                $('#myTab button').on('click', function (event) {
                    event.preventDefault();
                    $(this).tab('show');
                    const status = $(this).attr('aria-controls'); // e.g., 'pending'|'approved'|'rejected'
                    if (typeof getLeave === 'function') {
                        getLeave(status);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
