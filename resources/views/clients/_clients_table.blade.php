<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Managed Businesses / Clients</h5>
        <a href="{{ route('business.clients.create', $currentBusiness->slug) }}" class="btn btn-primary"> <i class="bi bi-plus-circle"></i> Add Clients </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="clientsTable" style="width: 100%">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Contact Person</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($managed_businesses as $managed_businesse)
                        <tr>
                            <td>{{ $managed_businesse->company_name }}</td>
                            <td></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning">
                                        <i class="bi bi-view-list"></i> Details
                                    </button>
                                    <button class="btn btn-danger">
                                        <i class="fa-solid fa-sign-in-alt"></i> Access
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
