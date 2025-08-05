<x-app-layout title="Create Lead">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between text-dark">
                        <h5 class="mb-0">Create Lead</h5>
                        <a href="{{ route('business.crm.leads.index', ['business' => session('active_business_slug')]) }}"
                            class="btn btn-secondary btn-sm">Back to Leads</a>
                    </div>
                    <div class="card-body">
                        <form id="leadForm"
                            data-redirect="{{ route('business.crm.leads.index', ['business' => session('active_business_slug')]) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="source" class="form-label">Source</label>
                                <input type="text" class="form-control" id="source" name="source">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="label" class="form-label">Label</label>
                                <select class="form-control" id="label" name="label">
                                    <option value="">None</option>
                                    <option value="High Priority">High Priority</option>
                                    <option value="Low Priority">Low Priority</option>
                                    <option value="Follow Up">Follow Up</option>
                                    <option value="Hot Lead">Hot Lead</option>
                                    <option value="Cold Lead">Cold Lead</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="campaign_id" class="form-label">Campaign</label>
                                <select class="form-control" id="campaign_id" name="campaign_id">
                                    <option value="">None</option>
                                    @foreach ($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton">Create Lead</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/leads.js') }}" type="module"></script>
    @endpush
</x-app-layout>