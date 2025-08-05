<table class="table table-striped table-hover" id="jobCategoriesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Job Category Name</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($job_categories as $index => $job_category)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $job_category->name }}</td>
                <td>{{ \Carbon\Carbon::parse($job_category->created_at)->diffForHumans() }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-info edit-job-category" onclick="editJobCategory(this)"
                            data-job-category="{{ $job_category->slug }}" data-bs-toggle="tooltip"
                            title="Edit Job Category">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger delete-job-category" id="submitButton"
                            onclick="deleteJobCategory(this)" data-job-category="{{ $job_category->slug }}"
                            data-bs-toggle="tooltip" title="Delete Job Category">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
