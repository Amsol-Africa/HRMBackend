<form id="locationsForm">
    <input type="hidden" name="location_slug" id="location_slug" value="{{ isset($location) &&!empty($location)? $location->slug: '' }}">

    <div class="form-group mb-3">
        <label for="name">Location Name</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Location Name" value="{{ isset($location) &&!empty($location)? $location->name: '' }}" required>
    </div>

    <div class="form-group mb-3">
        <label for="company_size">Company size</label>
        <select id="company_size" name="company_size" required class="form-select">
            <option value="">Select Company Size</option>
            <option value="1-10" {{ isset($location) &&!empty($location) && $location->company_size === '1-10'? 'selected': '' }}>1-10 employees</option>
            <option value="11-50" {{ isset($location) &&!empty($location) && $location->company_size === '11-50'? 'selected': '' }}>11-50 employees</option>
            <option value="51-200" {{ isset($location) &&!empty($location) && $location->company_size === '51-200'? 'selected': '' }}>51-200 employees</option>
            <option value="201-500" {{ isset($location) &&!empty($location) && $location->company_size === '201-500'? 'selected': '' }}>201-500 employees</option>
            <option value="500+" {{ isset($location) &&!empty($location) && $location->company_size === '500+'? 'selected': '' }}>500+ employees</option>
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="address">Physical Address</label>
        <input type="text" name="address" id="address" class="form-control" placeholder="Physical Address" value="{{ isset($location) &&!empty($location)? $location->physical_address: '' }}" required>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-primary w-100" onclick="saveLocation(this)">
            <i class="bi bi-check-circle"></i> Save Location
        </button>
    </div>
</form>
