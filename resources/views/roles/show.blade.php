<x-app-layout title="Role Details">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $role->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Business:</strong> {{ $role->business->company_name ?? 'N/A' }}</p>
                            <p><strong>Created:</strong> {{ $role->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Updated:</strong> {{ $role->updated_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Permissions</h6>
                            @if($role->permissions->isEmpty())
                            <p>No permissions assigned.</p>
                            @else
                            <ul class="list-group">
                                @foreach($role->permissions as $permission)
                                <li class="list-group-item">{{ $permission->name }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6>Assign Role to User</h6>
                        <form id="assignRoleForm" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select User <span
                                        class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a user.</div>
                            </div>
                            <button type="button" onclick="assignRole(this)" class="btn btn-primary">Assign
                                Role</button>
                        </form>
                    </div>
                    <div class="mt-4">
                        <h6>Users with this Role</h6>
                        <table class="table table-hover table-striped" id="assignedUsersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" data-user="{{ $user->id }}"
                                            data-role="{{ $role->id }}" onclick="removeRole(this)">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('business.roles.edit', ['business' => $businessModel->slug, 'role' => $role->name]) }}"
                            class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('business.roles.index', $businessModel->slug) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/roles.js') }}" type="module"></script>
    @endpush
</x-app-layout>