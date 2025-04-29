<x-app-layout title="Campaign Analytics">
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between text-dark">
                        <h5 class="mb-0">{{ $campaign->name }} - Analytics</h5>
                        <div>
                            <a href="{{ route('business.crm.campaigns.view', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}"
                                class="btn btn-outline-secondary btn-sm me-2">View Campaign</a>
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
                                <li class="breadcrumb-item"><a
                                        href="{{ route('business.crm.campaigns.view', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}">{{ $campaign->name }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                            </ol>
                        </nav>

                        <!-- Campaign Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Campaign Summary</h6>
                                <p><strong>Name:</strong> {{ $campaign->name }}</p>
                                <p><strong>Short Link:</strong>
                                    @if($campaign->shortLink)
                                <div class="input-group w-75">
                                    <input type="text" class="form-control"
                                        value="{{ url('/campaign/' . $campaign->shortLink->slug) }}" readonly
                                        id="shortLink">
                                    <button class="btn btn-outline-secondary copy-link" type="button"
                                        data-link="{{ url('/campaign/' . $campaign->shortLink->slug) }}">Copy</button>
                                </div>
                                @else
                                No short link generated.
                                @endif
                                </p>
                                <p><strong>Total Visits:</strong> {{ $campaign->shortLink->visits ?? 0 }}</p>
                                <p><strong>Has Survey:</strong> {{ $campaign->has_survey ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>

                        <!-- Short Link Visits -->
                        <h6>Short Link Visits</h6>
                        <div id="visitsTable">
                            <div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading visits...</div>
                        </div>

                        <!-- Survey Results (if applicable) -->
                        @if($campaign->has_survey)
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <h6>Survey Results</h6>
                            <a href="{{ route('business.crm.campaigns.surveys.export', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}"
                                class="btn btn-outline-primary btn-sm">Export to XLSX</a>
                        </div>
                        <div id="surveyTable">
                            <div class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading survey results...
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/campaigns.js') }}" type="module"></script>
    @endpush
</x-app-layout>