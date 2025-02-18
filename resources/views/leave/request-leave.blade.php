<x-app-layout>
    <div class="row mb-3">
        <h2>Request a Leave</h2>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    @include('leave._request_leave_form')
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    {{-- @include('leave._leave_requests_table') --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave.js') }}" type="module"></script>
    @endpush

</x-app-layout>
