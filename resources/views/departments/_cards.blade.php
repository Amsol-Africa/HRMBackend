<div class="row g-2">
    @foreach ($departments as $department)
        <div class="col-md-4">
            <x-department-card :department="$department" />
        </div>
    @endforeach
</div>
