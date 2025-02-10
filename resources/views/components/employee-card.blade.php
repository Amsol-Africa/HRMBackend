<div class="card__wrapper h-100 mb-0">
    <div class="employee__wrapper">
        <div class="text-center">
            <div class="employee__thumb mb-15">
                <a href=""><img src="{{ $employee->user->getImageUrl() }}" alt="{{ $employee->user->name }}"></a>
            </div>
        </div>
        <div class="employee__content">

            <div class="employee__meta">
                <div class="d-flex justify-content-center my-4" style="gap: 7px">
                    <span class="bd-badge bg-theme">{{ formatStatus($employee->user->status) }}</span>
                    <span class="bd-badge bg-warning">{{ formatStatus($employee->user->roles()->first()->name === 'business-employee' ? 'Employee' : $employee->user->roles()->first()->name)  }}</span>
                </div>

                <h5 class="mb-8"><a href="">{{ $employee->user->name }}</a></h5>

                <div class="row g-2 mb-8">
                    <div class="col-md-6">
                        <p class="mb-0"> <strong>Emp No. : </strong> {{ $employee->employee_code }}</p>
                        @if ($employee->location)
                            <p class="mb-0"> <strong>Location. : </strong> {{ $employee->location->name }}</p>
                        @endif
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

            <div class="employee__btn pb-0">
                <div class="row g-2">
                    <div class="col-md-6">
                        <a class="btn btn-primary btn-sm w-100" href="{{ route('business.employees.edit', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}"> <i class="bi bi-pencil-square"></i> Update</a>
                    </div>
                    <div class="col-md-6">
                        <a class="btn btn-success btn-sm w-100" href="{{ route('business.employees.details', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}"><i class="bi bi-view-list"></i> Details </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
