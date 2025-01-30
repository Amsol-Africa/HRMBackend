<div class="row g-2">
    @if ($employees->isEmpty())
        <x-warning-card
            message="No employees found."
            longText="It looks like there are no employees matching your criteria."
            icon="fa-info-circle"
            bgColor="bg-warning"
            textColor="text-light"
        />
    @else
        @foreach ($employees as $employee)
            <div class="col-md-3">
                <x-employee-card :employee="$employee" />
            </div>
        @endforeach
    @endif
</div>
