<x-app-layout>

    <div class="row g-20 mb-20 justify-content-between align-items-end">
        <div class="col-xxl-3 col-xl-5 col-lg-4 col-md-4">
            <div class="card__wrapper">
                <div class="search-box">
                    <input type="text" class="form-control" id="employeeName" placeholder="Employee Name">
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-5 col-lg-4 col-md-4">
            <div class="card__wrapper">
                <div class="search-box">
                    <input type="text" class="form-control" id="employeeId" placeholder="Employee ID">
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-5 col-lg-4 col-md-4">
            <div class="card__wrapper">
                <div class="from__input-box">
                    <select class="js-example-basic-single">
                        <option value="">Employee Depaertment</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->slug }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-5 col-lg-4 col-md-4">
            <div class="card__wrapper">
                <div class="d-flex align-items-center justify-content-between gap-15">
                    <button type="button" onclick="searchEmployees(this)" class="btn btn-secondary"> <i class="bi bi-funnel"></i> Filters</button>
                    <a href="{{ route('business.employees.create', $currentBusiness->slug) }}" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addNewEmployee"> Add Employee</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-20" id="employeesContainer">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body"> {{ loader() }} </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/employees.js') }}" type="module"></script>
        <script>
            $(document).ready(() => {
                getEmployees()
            })
        </script>

    @endpush

</x-app-layout>
