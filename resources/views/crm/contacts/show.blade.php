<x-app-layout title="View Contact Submission">
    <div class="container-fluid my-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Contact Submission Details</h5>
                        <a href="{{ route('business.crm.contacts.index', ['business' => $currentBusiness->slug]) }}"
                            class="btn btn-secondary btn-sm">← Back to Contacts</a>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Name</h6>
                                <p class="mb-0">
                                    {{ trim($submission->first_name . ' ' . $submission->last_name) ?: 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Email</h6>
                                <p class="mb-0">{{ $submission->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Phone</h6>
                                <p class="mb-0">{{ $submission->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Company</h6>
                                <p class="mb-0">{{ $submission->company_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Inquiry Type</h6>
                                <p class="mb-0">{{ $submission->inquiry_type ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Status</h6>
                                <p class="mb-0">
                                    <span
                                        class="badge bg-{{ $submission->status === 'new' ? 'info' : ($submission->status === 'qualified' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Source</h6>
                                <p class="mb-0">{{ $submission->source ?? 'Unknown' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">UTM Parameters</h6>
                                <p class="mb-0">
                                    <strong>Source:</strong> {{ $submission->utm_source ?? 'N/A' }}<br>
                                    <strong>Medium:</strong> {{ $submission->utm_medium ?? 'N/A' }}<br>
                                    <strong>Campaign:</strong> {{ $submission->utm_campaign ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted">Message</h6>
                                <div class="mb-3">
                                    {{ $submission->message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>