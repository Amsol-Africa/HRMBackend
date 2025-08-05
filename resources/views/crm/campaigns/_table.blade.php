<table class="table table-hover table-striped" id="campaignsDataTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>UTM Source</th>
            <th>UTM Medium</th>
            <th>Short Link</th>
            <th>Visits</th>
            <th>Survey</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($campaigns as $campaign)
        <tr>
            <td>{{ $campaign->name }}</td>
            <td>{{ $campaign->utm_source }}</td>
            <td>{{ $campaign->utm_medium }}</td>
            <td>
                @if($campaign->shortLink)
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" value="{{ url('/campaign/' . $campaign->shortLink->slug) }}"
                        readonly>
                    <button class="btn btn-outline-secondary copy-link" type="button"
                        data-link="{{ url('/campaign/' . $campaign->shortLink->slug) }}">Copy</button>
                </div>
                @else
                N/A
                @endif
            </td>
            <td>{{ $campaign->shortLink ? $campaign->shortLink->visits : 0 }}</td>
            <td>{{ $campaign->has_survey ? 'Yes' : 'No' }}</td>
            <td>{{ ucfirst($campaign->status) }}</td>
            <td>
                <a href="{{ route('business.crm.campaigns.view', ['business' => $currentBusiness->slug, 'campaign' => $campaign->id]) }}"
                    class="btn btn-sm btn-outline-primary">View</a>
                <button class="btn btn-sm btn-outline-danger delete-campaign"
                    data-id="{{ $campaign->id }}">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>