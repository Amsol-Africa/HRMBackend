<form id="departmentsForm" method="post">
    @csrf
    @if(isset($department))
        <input type="hidden" name="department_slug" value="{{ $department->slug }}">
    @endif

    <div class="form-group mb-3">
        <label for="department_name">Department Name</label>
        <input type="text" class="form-control" id="department_name" name="department_name" required placeholder="e.g Finance" value="{{ isset($department) ? $department->name : old('department_name') }}">
    </div>

    <div class="form-group mb-3">
        <label for="description">Department Description</label>
        <textarea name="description" id="description" class="form-control" rows="4">{{ isset($department) ? $department->description : 'Short Department Description...' }}</textarea>
    </div>

    <div>
        <button onclick="saveDepartment(this)" type="button" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> {{ isset($department) ? 'Update Department' : 'Save Department' }}
        </button>
    </div>
</form>
