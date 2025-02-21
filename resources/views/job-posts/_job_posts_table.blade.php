<table class="table table-hover table-bordered table-striped" id="jobPostsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Job Title</th>
            <th>Employment Type</th>
            <th>Location</th>
            <th>Posted At</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($job_posts as $index => $job)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $job->title }}</td>
                <td>{{ ucfirst($job->employment_type) }}</td>
                <td>{{ $job->place }}</td>
                <td>{{ $job->created_at->format('d M Y') }}</td>
                <td>
                    <span class="badge bg-{{ $job->status == 'open' ? 'success' : 'secondary' }}">
                        {{ ucfirst($job->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('business.recruitment.jobs.edit', ['business' => $currentBusiness->slug, 'jobpost' => $job->slug]) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <button class="btn btn-danger btn-sm" type="button" data-job-post="{{ $job->slug }}" onclick="deleteJobPost(this)">
                            <i class="bi bi-trash"></i> Delete
                      </button>

                    <a href="" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
