<table class="table table-hover table-striped" id="rolesTable">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $index => $role)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $role->name }}</td>
            <td>{{ $role->created_at->format('d M Y') }}</td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{ route('roles.show', $role->name) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ route('roles.edit', $role->name) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button class="btn btn-danger btn-sm" data-role="{{ $role->name }}" onclick="deleteRole(this)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>