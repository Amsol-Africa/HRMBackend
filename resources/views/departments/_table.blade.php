<table class="table table-striped table-hover" id="departmentsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Department Name</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($departments as $index => $department)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $department->name }}</td>
                <td>{{ \Carbon\Carbon::parse($department->created_at)->diffForHumans() }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-info edit-department" onclick="editDepartment(this)"
                            data-department="{{ $department->slug }}" data-bs-toggle="tooltip" title="Edit Department">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger delete-department" id="submitButton"
                            onclick="deleteDepartment(this)" data-department="{{ $department->slug }}"
                            data-bs-toggle="tooltip" title="Delete Department">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
