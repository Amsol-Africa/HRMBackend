<x-app-layout>
    <div class="container py-4">
        <h2 class="fw-bold mb-4">Welcome Wayne</h2>

        <!-- Stats Overview -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">Total Leaves</h5>
                    <h4 class="fw-bold text-dark">{{ $leave_count ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <i class="bi bi-clock text-success" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">Days Worked</h5>
                    <h4 class="fw-bold text-dark">{{ $work_days ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <i class="bi bi-calendar-check text-warning" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">Pending Leaves</h5>
                    <h4 class="fw-bold text-dark">{{ $pending_leaves ?? 0 }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <i class="bi bi-receipt text-info" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">Payslips</h5>
                    <h4 class="fw-bold text-dark">{{ $payslips ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle text-primary" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Profile</h6>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-3">View
                            Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check text-warning" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Leave Requests</h6>
                        <a href="#" class="btn btn-outline-warning btn-sm mt-3">Request
                            Leave</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2">Sign-in</h6>
                        <a href="#" class="btn btn-outline-success btn-sm mt-3">Check In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>