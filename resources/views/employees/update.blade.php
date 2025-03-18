<x-app-layout>

    <div class="breadcrumb__area">
        <div class="breadcrumb__wrapper mb-25">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Employee Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                type="button" role="tab" aria-controls="profile" aria-selected="true">Employee Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payrolls-tab" data-bs-toggle="tab" data-bs-target="#payrolls" type="button"
                role="tab" aria-controls="payrolls" aria-selected="false">Payrolls</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button"
                role="tab" aria-controls="leave" aria-selected="false">Leave</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance"
                type="button" role="tab" aria-controls="attendance" aria-selected="false">Time &
                Attendance</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div id="profileContainer">

                @include('employees._profile_details')

            </div>
        </div>
        <div class="tab-pane fade" id="payrolls" role="tabpanel" aria-labelledby="payrolls-tab">
            <div id="payrollsContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
            <div id="leaveContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="attandance-tab">
            <div id="attendanceContainer">
                <div class="card">
                    <div class="card-body"> {{ loader() }} </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
    @endpush

</x-app-layout>
