<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row g-4">
            <div class="col-12">
                <h1 class="mb-3">Work Rosters</h1>
                <p class="text-muted">Manage and schedule employee rosters across departments and locations.</p>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="card-header">Add New Roster</h5>
                    </div>
                    <div class="card-body" id="rostersFormContainer">
                        @include('roster._form')
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Roster Assignments</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="toggleView">
                                <i class="fas fa-table"></i> Toggle View
                            </button>
                            <button class="btn btn-success btn-sm" id="generateReport">
                                <i class="fas fa-chart-bar"></i> Generate Report
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="exportCsv">CSV</a></li>
                                    <li><a class="dropdown-item" href="#" id="exportPdf">PDF</a></li>
                                    <li><a class="dropdown-item" href="#" id="exportExcel">Excel</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4 g-2">
                            <div class="col-md-3">
                                <select class="form-select" id="filterDepartment">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterJobCategory">
                                    <option value="">All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                    <option value="{{ $jobCategory->id }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterLocation">
                                    <option value="">All Locations</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterEmployee">
                                    <option value="">All Employees</option>
                                    @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->user->first_name }}
                                        {{ $employee->user->last_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="rostersContainer" class="table-responsive">
                            <div class="text-center py-4">{{ loader() }}</div>
                        </div>
                        <div id="calendarContainer" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <style>
    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .fc-event {
        cursor: pointer;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="{{ asset('js/main/rosters.js') }}"></script>
    @endpush
</x-app-layout>