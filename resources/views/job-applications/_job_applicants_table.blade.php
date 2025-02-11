<table class="table table-hover table-bordered table-striped" id="jobApplicantsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Applicant Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Location</th>
            <th>Experience Level</th>
            <th>Current Job Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($applicants as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
                <td>{{ $user->applicant->city }}, {{ $user->applicant->country }}</td>
                <td>{{ ucfirst($user->applicant->experience_level) }}</td>
                <td>{{ $user->applicant->current_job_title ?? 'N/A' }}</td>
                <td>
                    <a href="" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this applicant?')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
