<table class="table table-striped table-hover" id="clientsTable" style="width: 100%">
    <thead>
        <tr>
            <th>Business Name</th>
            <th>Industry</th>
            <th>Contact Person</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($managed_businesses as $managed_business)
            <tr>
                <td>
                    <a href="" class="flex-center">
                        <img class="img-48 border-circle" src="{{ $managed_business->getImageUrl() }}" alt="{{ $managed_business->company_name }}">
                        <span>{{ $managed_business->company_name }}</span>
                    </a>
                </td>
                <td>{{ ucfirst($managed_business->industry) }}</td>
                <td>{{ $managed_business->phone }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-warning">
                            <i class="bi bi-view-list"></i> Details
                        </button>
                        <button type="button" onclick="bsImpersonate(this)" data-business="{{ $managed_business->slug }}" class="btn btn-danger">
                            <i class="fa-solid fa-sign-in-alt"></i> Access
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
