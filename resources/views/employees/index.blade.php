<x-app-layout>





    <div class="col-md-12">
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">Active</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="on-leave-tab" data-bs-toggle="tab" data-bs-target="#on-leave" type="button" role="tab" aria-controls="on-leave" aria-selected="false">On Leave</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notice-exit-tab" data-bs-toggle="tab" data-bs-target="#notice-exit" type="button" role="tab" aria-controls="notice-exit" aria-selected="false">On Notice of Exit</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive" type="button" role="tab" aria-controls="inactive" aria-selected="false">Inactive</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="exited-tab" data-bs-toggle="tab" data-bs-target="#exited" type="button" role="tab" aria-controls="exited" aria-selected="false">Exited</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="false">All</button>
            </li>
        </ul>

        <div class="card__wrapper">
            <div class="row">
                <div class="col-md-3">
                    <div class="search-box">
                        <input type="text" class="form-control" id="employeeName" placeholder="Employee Name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="search-box">
                        <input type="text" class="form-control" id="employeeNo" placeholder="Employee Number">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="from__input-box">
                        <select class="form-select" id="employeeDepartment">
                            <option value="">Employee Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->slug }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="from__input-box">
                        <select class="form-select" id="employeeGender">
                            <option value="">Employee Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center justify-content-between gap-15">
                        <button type="button" onclick="searchEmployees(this)" class="btn btn-secondary"> <i class="bi bi-funnel"></i> Filters</button>
                        <a href="{{ route('business.employees.create', $currentBusiness->slug) }}" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addNewEmployee"> <i class="bi bi-plus-square"></i> Add</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                <div id="activeContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="on-leave" role="tabpanel" aria-labelledby="on-leave-tab">
                <div id="on-leaveContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="notice-exit" role="tabpanel" aria-labelledby="notice-exit-tab">
                <div id="notice-exitContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
                <div id="inactiveContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="exited" role="tabpanel" aria-labelledby="exited-tab">
                <div id="exitedContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div id="allContainer">
                    <div class="card">
                        <div class="card-body"> {{ loader() }} </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                let savedStatus = localStorage.getItem('employeeStatus') || 'active';

                getEmployees(1, savedStatus);

                $('#myTab button').on('click', function (event) {
                    event.preventDefault();
                    $(this).tab('show');
                    const status = $(this).attr('aria-controls');
                    localStorage.setItem('employeeStatus', status);
                    getEmployees(1, status);
                });
            });
        </script>
    @endpush

</x-app-layout>
