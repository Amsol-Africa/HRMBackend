<table class="table table-striped table-hover" id="locationsTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Company Size</th>
            {{-- <th>Registration No</th>
            <th>Tax Pin No</th>
            <th>Business License No</th> --}}
            <th>Physical Address</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($locations as $location)
            <tr>
                <td>{{ $location->name }}</td>
                <td>{{ $location->company_size }}</td>
                {{-- <td>{{ $location->registration_no }}</td>
                <td>{{ $location->tax_pin_no }}</td>
                <td>{{ $location->business_license_no }}</td> --}}
                <td>{{ $location->physical_address }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary view-location" data-location="{{ $location->slug }}"
                            data-bs-toggle="modal" data-bs-target="#locationDetailsModal" data-bs-toggle="tooltip"
                            title="View Location">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-info edit-location" onclick="editLocation(this)"
                            data-location="{{ $location->slug }}" data-bs-toggle="tooltip" title="Edit Location">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger delete-location" onclick="deleteLocation(this)"
                            data-location="{{ $location->slug }}" data-bs-toggle="tooltip" title="Delete Location">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
