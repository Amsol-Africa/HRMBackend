<div class="card__wrapper h-100">
    <div class="employee__wrapper">
        <div class="text-center">
            <div class="employee__thumb mb-15">
                <a href=""><img src="{{ $employee->user->getImageUrl() }}" alt="{{ $employee->user->name }}"></a>
            </div>
        </div>
        <div class="employee__content">
            <div class="employee__meta mb-15">
                <h5 class="mb-8"><a href="">{{ $employee->user->name }}</a></h5>
                <p class="mb-0"> <strong>Emp No. : </strong> {{ $employee->employee_code }}</p>
                <p class="mb-0"> <strong>Role : </strong> {{ ucfirst($employee->user->roles()->first()->name) }}</p>
                <p class="mb-0"> <strong>Department : </strong> {{ $employee->department->name }}</p>
                <p class="mb-0"> <strong>Contact : </strong> {{ $employee->user->phone }}</p>
            </div>
            <div class="employee__btn">
                <div class="d-flex align-items-center justify-content-center gap-15">
                    <a class="btn btn-warning" href="tel:{{ $employee->user->phone }}"> <i class="bi bi-phone"></i> Call</a>
                    <a class="btn btn-primary" href="{{ route('business.employees.edit', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}"> <i class="fa-solid fa-edit"></i> Update</a>
                    <a class="btn btn-success" href="{{ route('business.employees.details', ['business' => $currentBusiness->slug, 'employee' => $employee->user->id]) }}">View <i class="bi bi-arrow-right ms-2"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>
