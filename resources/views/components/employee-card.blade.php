<div class="card__wrapper h-100">
    <div class="employee__wrapper">
        <div class="text-center">
            <div class="employee__thumb mb-15">
                <a href=""><img src="{{ $employee->user->getImageUrl() }}" alt="{{ $employee->user->name }}"></a>
            </div>
        </div>
        <div class="employee__content">
            <div class="employee__meta mb-15">
                <div class="d-flex justify-content-center my-4" style="gap: 7px">
                    <span class="bd-badge bg-theme">{{ formatStatus($employee->user->status) }}</span>
                    <span class="bd-badge bg-warning">{{ formatStatus($employee->user->roles()->first()->name) }}</span>
                </div>

                <h5 class="mb-8"><a href="">{{ $employee->user->name }}</a></h5>
                <div class="row g-2 mb-8">
                    <div class="col-md-6">
                        <p class="mb-0"> <strong>Emp No. : </strong> {{ $employee->employee_code }}</p>
                        <p class="mb-0"> <strong>ID No. : </strong> {{ $employee->national_id }}</p>
                        <p class="mb-0"> <strong>Contact : </strong> {{ $employee->user->phone }}</p>
                        <p class="mb-0"> <strong>Gender.: </strong> {{ $employee->gender }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-0"> <strong>Department : </strong> {{ $employee->department->name }}</p>
                        <p class="mb-0"> <strong>Emp Type.: </strong> {{ formatStatus($employee->employmentDetails->employment_term) }}</p>
                        <p class="mb-0"> <strong>Emp Date.: </strong> {{ date("jS M, Y", strtotime($employee->employmentDetails->employment_date)) }}</p>
                    </div>
                </div>

            </div>
            <div class="employee__btn">
                <div class="d-flex align-items-center justify-content-center" style="gap: 2px">
                    <a class="btn btn-warning" href=""> <i class="bi bi-wallet2"></i> Salary</a>
                    <a class="btn btn-primary" href="{{ route('business.employees.edit', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}"> <i class="fa-solid fa-edit"></i> Update</a>
                    <a class="btn btn-success" href="{{ route('business.employees.details', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}">View <i class="bi bi-arrow-right ms-2"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>
