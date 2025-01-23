<div class="row g-2">
    @foreach ($employees as $employee)
        <div class="col-md-3">
            <x-employee-card :employee="$employee" />
        </div>
    @endforeach
</div>
