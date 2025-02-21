<x-app-layout>

    <div class="col-md-12">
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            @php
                $tabs = [
                    'active' => 'Active',
                    'on-leave' => 'On Leave',
                    'notice-exit' => 'On Notice of Exit',
                    'inactive' => 'Inactive',
                    'exited' => 'Exited',
                    'all' => 'All',
                ];
            @endphp

            @foreach ($tabs as $key => $value)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="{{ $key }}-tab" data-status="{{ $key }}"
                        data-bs-toggle="tab" data-bs-target="#{{ $key }}" type="button" role="tab"
                        aria-controls="{{ $key }}" aria-selected="false">{{ $value }}</button>
                </li>
            @endforeach
        </ul>

        <div class="card__wrapper">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control" id="employeeName" placeholder="Employee Name">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="employeeNo" placeholder="Employee No">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="location">
                        <option value="">Location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->slug }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="employeeDepartment">
                        <option value="">Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->slug }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="employeeGender">
                        <option value="">Employee Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex align-items-center gap-15">
                        <button type="button" onclick="searchEmployees(this)" class="btn btn-secondary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('business.employees.create', $currentBusiness->slug) }}"
                            class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewEmployee">
                            <i class="bi bi-plus-square"></i> Add
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            @foreach ($tabs as $key => $value)
                <div class="tab-pane fade" id="{{ $key }}" role="tabpanel"
                    aria-labelledby="{{ $key }}-tab">
                    <div class="card-body">
                        <div class="table-responsive" id="{{ $key }}Employees">
                            <div class="text-center">{{ loader() }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                let savedStatus = localStorage.getItem('employeeStatus') || 'active';

                // Load employees based on saved tab
                getEmployees(1, savedStatus);
                $(`#${savedStatus}-tab`).addClass('active');
                $(`#${savedStatus}`).addClass('show active');

                $('#myTab button').on('click', function() {
                    const status = $(this).data('status');
                    localStorage.setItem('employeeStatus', status);
                    getEmployees(1, status);
                });
            });
        </script>
    @endpush

</x-app-layout>
