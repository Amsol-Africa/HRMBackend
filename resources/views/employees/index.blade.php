<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">Employees</h2>
                    <span id="employeeCount"
                        class="badge bg-primary-soft text-primary px-3 py-2">{{ $employees->count() }} Employees</span>
                </div>

                <!-- Filters -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" id="search" class="form-control"
                                    placeholder="Search by name or code...">
                            </div>
                            <div class="col-md-3">
                                <select id="filterDepartment" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterLocation" class="form-select">
                                    <option value="">All Locations</option>
                                    <option value="{{ $business->business_id }}">{{ $business->company_name }} | Main
                                        business</option>
                                    @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterJobCategory" class="form-select">
                                    <option value="">All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                    <option value="{{ $jobCategory->id }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" onclick="createEmployee()">Add Employee</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div id="employeesContainer" class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        @include('employees._table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="employeeFormContainer">
                    <!-- Form will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>


    <!-- View Modal -->
    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewEmployeeModalLabel">Employee Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewEmployeeContainer"></div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @endpush
</x-app-layout>