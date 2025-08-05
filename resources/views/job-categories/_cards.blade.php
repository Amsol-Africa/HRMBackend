<div class="row g-2">
    @foreach ($job_categories as $job_category)
        <div class="col-md-4">
            <x-job-category-card :job_category="$job_category" />
        </div>
    @endforeach
</div>
