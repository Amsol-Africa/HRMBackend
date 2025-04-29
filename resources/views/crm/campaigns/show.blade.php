<x-app-layout title="View Campaign">
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between text-dark">
                        <h5 class="mb-0">{{ $campaign->name }}</h5>
                        <div>
                            <a href="{{ route('business.crm.campaigns.analytics', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}"
                                class="btn btn-outline-primary btn-sm me-2">View Analytics</a>
                            <a href="{{ route('business.crm.campaigns.index', ['business' => $currentBusiness->slug]) }}"
                                class="btn btn-secondary btn-sm">Back to Campaigns</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('business.index', ['business' => $currentBusiness->slug]) }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('business.crm.campaigns.index', ['business' => $currentBusiness->slug]) }}">Campaigns</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $campaign->name }}</li>
                            </ol>
                        </nav>

                        <!-- Campaign Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Details</h6>
                                <p><strong>Name:</strong> {{ $campaign->name }}</p>
                                <p><strong>UTM Source:</strong> {{ $campaign->utm_source }}</p>
                                <p><strong>UTM Medium:</strong> {{ $campaign->utm_medium }}</p>
                                <p><strong>UTM Campaign:</strong> {{ $campaign->utm_campaign }}</p>
                                <p><strong>Target URL:</strong> <a href="{{ $campaign->target_url }}"
                                        target="_blank">{{ $campaign->target_url }}</a></p>
                                <p><strong>Start Date:</strong> {{ $campaign->start_date }}</p>
                                <p><strong>End Date:</strong> {{ $campaign->end_date ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($campaign->status) }}</p>
                                <p><strong>Has Survey:</strong> {{ $campaign->has_survey ? 'Yes' : 'No' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Short Link</h6>
                                @if($campaign->shortLink)
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control"
                                        value="{{ url('/campaign/' . $campaign->shortLink->slug) }}" readonly
                                        id="shortLink">
                                    <button class="btn btn-outline-secondary copy-link" type="button"
                                        data-link="{{ url('/campaign/' . $campaign->shortLink->slug) }}">Copy</button>
                                </div>
                                <p><strong>Visits:</strong> {{ $campaign->shortLink->visits }}</p>
                                @else
                                <p>No short link generated.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Related Leads -->
                        <h6 class="mt-4">Related Leads</h6>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->leads as $lead)
                                <tr>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->email }}</td>
                                    <td>{{ $lead->phone ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($lead->status) }}</td>
                                    <td>
                                        <a href="{{ route('business.crm.leads.view', ['business' => $currentBusiness->slug, 'lead' => $lead->id]) }}"
                                            class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/campaigns.js') }}" type="module"></script>
    @endpush
</x-app-layout>