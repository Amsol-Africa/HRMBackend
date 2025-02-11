<table class="table table-striped" id="jobApplicationsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Applicant</th>
            <th>Phone</th>
            <th>Job Title</th>
            <th>Status</th>
            <th>Applied On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($applications as $index => $application)
            <tr>
                <td>{{ $applications->firstItem() + $index }}</td>
                <td>
                    <div class="d-flex align-items-center" style="gap: 3px">
                        <span class="table-avatar">
                            <a class="employee__avatar mr-5" href="">
                                <img class="img-48 border-circle" src="{{ $application->applicant->user->getImageUrl() }}" alt="{{ $application->applicant->user->name }}">
                            </a>
                        </span>
                        <span>
                            <strong>{{ $application->applicant->user->name }}</strong> <br>
                            <small class="text-muted">{{ $application->applicant->user->email }}</small>
                        </span>
                    </div>
                </td>
                <td>{{ $application->applicant->user->phone }}</td>
                <td>{{ $application->jobPost->title }}</td>
                <td>
                    <span class="badge bg-{{ $application->status === 'applied' ? 'primary' : 'success' }}">
                        {{ ucfirst($application->status) }}
                    </span>
                </td>
                <td>{{ $application->created_at->format('d M, Y') }}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewApplication({{ $application->id }})">
                        <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteApplication({{ $application->id }})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="openScheduleInterviewModal({{ $application->id }}, '{{ $application->applicant->user->name }}', '{{ $application->jobPost->title }}')">
                        <i class="bi bi-calendar-plus"></i> Schedule Interview
                    </button>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No applications found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

