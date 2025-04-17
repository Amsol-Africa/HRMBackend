<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Application Details: {{ $application->applicant->user->name }}</h5>
                        <a href="{{ route('business.applications.index', $currentBusiness->slug) }}"
                            class="btn btn-light btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Back to Applications
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Applicant Information</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">Name:</dt>
                                    <dd class="col-sm-8">{{ $application->applicant->user->name }}</dd>
                                    <dt class="col-sm-4">Email:</dt>
                                    <dd class="col-sm-8">{{ $application->applicant->user->email }}</dd>
                                    <dt class="col-sm-4">Phone:</dt>
                                    <dd class="col-sm-8">{{ $application->applicant->user->phone }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Application Details</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">Job Title:</dt>
                                    <dd class="col-sm-8">{{ $application->jobPost->title }}</dd>
                                    <dt class="col-sm-4">Stage:</dt>
                                    <dd class="col-sm-8">
                                        <span
                                            class="badge bg-{{ $application->stage === 'applied' ? 'primary' : ($application->stage === 'shortlisted' ? 'success' : ($application->stage === 'rejected' ? 'danger' : 'info')) }}">
                                            {{ ucfirst($application->stage) }}
                                        </span>
                                    </dd>
                                    <dt class="col-sm-4">Applied On:</dt>
                                    <dd class="col-sm-8">{{ $application->created_at->format('d M, Y') }}</dd>
                                    <dt class="col-sm-4">Cover Letter:</dt>
                                    <dd class="col-sm-8">{!! $application->cover_letter ?? 'N/A' !!}</dd>
                                </dl>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted">Attachments</h6>
                        @if($application->getMedia('applications')->isEmpty())
                        <p class="text-muted">No attachments available.</p>
                        @else
                        <ul class="list-group">
                            @foreach($application->getMedia('applications') as $media)
                            <li class="list-group-item">
                                <button class="btn btn-sm btn-primary"
                                    data-applicant-id="{{ $application->applicant->id }}"
                                    data-media-id="{{ $media->id }}" onclick="downloadDocument(this)">
                                    <i class="bi bi-download"></i> {{ $media->file_name }}
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <hr class="my-4">
                        <h6 class="text-muted">Interviews</h6>
                        @if($application->interviews->isEmpty())
                        <p class="text-muted">No interviews scheduled.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($application->interviews as $index => $interview)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $interview->scheduled_at->toDayDateTimeString() }}</td>
                                        <td>{{ $interview->location ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($interview->type) }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $interview->status === 'scheduled' ? 'info' : ($interview->status === 'completed' ? 'success' : ($interview->status === 'cancelled' ? 'danger' : 'warning')) }}">
                                                {{ ucfirst($interview->status ?? 'Not Set') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#editApplicationModal" onclick="editJobApplication({{ $application->id }})">
                            <i class="fa-solid fa-edit"></i> Edit Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Application Modal -->
    <div class="modal fade" id="editApplicationModal" tabindex="-1" role="dialog"
        aria-labelledby="editApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editApplicationModalLabel">Edit Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-application-form">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/job-applications.js') }}" type="module"></script>
    <script>
    window.csrfToken = '{{ csrf_token() }}';
    </script>
    @endpush
</x-app-layout>