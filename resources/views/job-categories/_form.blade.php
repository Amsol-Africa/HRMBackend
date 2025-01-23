<form id="jobCategoriesForm" method="post">
    @csrf
    @if(isset($job_category))
        <input type="hidden" name="job_category_slug" value="{{ $job_category->slug }}">
    @endif

    <div class="form-group mb-3">
        <label for="job_category">Job Category Name</label>
        <input type="text" class="form-control" id="job_category" name="job_category" required placeholder="e.g Finance" value="{{ isset($job_category) ? $job_category->name : old('job_category') }}">
    </div>

    <div class="form-group mb-3">
        <label for="description">Job Category Description</label>
        <textarea name="description" id="description" class="form-control" rows="4">{{ isset($job_category) ? $job_category->description : 'Short Job Category Description...' }}</textarea>
    </div>

    <div>
        <button onclick="saveJobCategory(this)" type="button" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> {{ isset($job_category) ? 'Update Job Category' : 'Save Job Category' }}
        </button>
    </div>
</form>
