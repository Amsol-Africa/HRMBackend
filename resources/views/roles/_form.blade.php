<form id="roleForm" class="needs-validation" novalidate>
    @csrf
    @isset($role)
    <input type="hidden" name="role_name" value="{{ $role->name }}">
    @endisset

    <div class="row g-2">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Role Details</h5>
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name"
                            value="@isset($role){{ $role->name }}@endisset" required>
                        <div class="invalid-feedback">Please enter a role name.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="permissions" class="form-label">Permissions</label>
                        <select class="form-select" name="permissions[]" multiple size="5">
                            @foreach($permissions as $permission)
                            <option value="{{ $permission->id }}" @isset($role) @if($role->
                                permissions->contains($permission->id)) selected @endif @endisset>
                                {{ $permission->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="saveRole(this)" class="btn btn-primary w-100">
                        <i class="fa-regular fa-check-circle me-1"></i> @isset($role) Update @else Create @endisset Role
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('roleForm');
    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>