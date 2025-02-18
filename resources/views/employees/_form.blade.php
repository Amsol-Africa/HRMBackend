<form id="employeesForm" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-person"></i> Bio Information</h4>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-12 mb-4">
                    <label for="location">Choose location</label>
                    <select name="location" id="location" class="form-select">
                        <option>Choose location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->slug }}"
                                {{ $employee->location == $location->slug ? 'selected' : '' }}>{{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    <p><i>Leave empty to add employee to main business</i></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="last_name">Surname</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required
                        placeholder="Last Name" value="{{ $employee->last_name ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required
                        placeholder="First Name" value="{{ $employee->first_name ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name"
                        placeholder="Middle Name" value="{{ $employee->middle_name ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="male" {{ $employee->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $employee->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        placeholder="Personal e-mail Address" value="{{ $employee->email ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="phone0">Phone</label>
                    <input type="text" class="phone-input-control" id="phone0" name="phone" required
                        value="{{ $employee->phone ?? '' }}">
                    <input type="text" hidden id="code0" name="phone_code" required
                        value="{{ $employee->phone_code ?? '' }}">
                    <input type="text" hidden id="country0" name="phone_country" required
                        value="{{ $employee->phone_country ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="phone1">Alternate Phone No.</label>
                    <input type="text" class="phone-input-control" id="phone1" name="alternate_phone"
                        value="{{ $employee->alternate_phone ?? '' }}">
                    <input type="text" hidden id="code1" name="alternate_phone_code"
                        value="{{ $employee->alternate_phone_code ?? '' }}">
                    <input type="text" hidden id="country1" name="alternate_phone_country"
                        value="{{ $employee->alternate_phone_country ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="text" class="form-control datepicker" id="date_of_birth" name="date_of_birth"
                        placeholder="Date of Birth" value="{{ $employee->date_of_birth ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" class="form-control" id="place_of_birth" name="place_of_birth"
                        placeholder="Place of Birth" value="{{ $employee->place_of_birth ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="marital_status">Marital Status</label>
                    <select name="marital_status" id="marital_status" class="form-select">
                        <option>-Select-</option>
                        <option value="single" {{ $employee->marital_status == 'single' ? 'selected' : '' }}>Single
                        </option>
                        <option value="married" {{ $employee->marital_status == 'married' ? 'selected' : '' }}>Married
                        </option>
                        <option value="divorced" {{ $employee->marital_status == 'divorced' ? 'selected' : '' }}>
                            Divorced</option>
                        <option value="widowed" {{ $employee->marital_status == 'widowed' ? 'selected' : '' }}>Widowed
                        </option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="national_id">ID Number</label>
                    <input type="text" class="form-control" id="national_id" name="national_id"
                        placeholder="National ID" value="{{ $employee->national_id ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="place_of_issue">Place of Issue</label>
                    <input type="text" class="form-control" id="place_of_issue" name="place_of_issue"
                        placeholder="Place of Issue" value="{{ $employee->place_of_issue ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="tax_no">Tax No.</label>
                    <input type="text" class="form-control" id="tax_no" name="tax_no" placeholder="Tax No."
                        value="{{ $employee->tax_no ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="nhif_no">NHIF No.</label>
                    <input type="text" class="form-control" id="nhif_no" name="nhif_no" placeholder="NHIF No."
                        value="{{ $employee->nhif_no ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="nssf_no">NSSF No.</label>
                    <input type="text" class="form-control" id="nssf_no" name="nssf_no" placeholder="NSSF No."
                        value="{{ $employee->nssf_no ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="passport_no">Passport No.</label>
                    <input type="text" class="form-control" id="passport_no" name="passport_no"
                        placeholder="Passport No." value="{{ $employee->passport_no ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="passport_issue_date">Passport Issue Date.</label>
                    <input type="text" class="form-control datepicker" id="passport_issue_date"
                        name="passport_issue_date" placeholder="Passport Issue Date"
                        value="{{ $employee->passport_issue_date ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="passport_expiry_date">Passport Expiry Date.</label>
                    <input type="text" class="form-control datepicker" id="passport_expiry_date"
                        name="passport_expiry_date" placeholder="Passport Expiry Date"
                        value="{{ $employee->passport_expiry_date ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="address">Residential Address</label>
                    <input type="text" class="form-control" id="address" name="address"
                        placeholder="Residential Address" value="{{ $employee->address ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="permanent_address">Permanent Residential Address</label>
                    <input type="text" class="form-control" id="permanent_address" name="permanent_address"
                        placeholder="Permanent Residential Address" value="{{ $employee->permanent_address ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="blood_group">Blood Group</label>
                    <select name="blood_group" id="blood_group" class="form-select">
                        <option value="" selected disabled>Select Blood Group</option>
                        <option value="A+" {{ $employee->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ $employee->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ $employee->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ $employee->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ $employee->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ $employee->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ $employee->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ $employee->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="avatar">Profile Picture</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                    @if ($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" alt="Current Avatar" width="100">
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="from__input-box">
                        <label for="password">Password <strong>(Leave empty if employee should not login)</strong>
                        </label>
                        <div class="form__input">
                            <input class="form-control" placeholder="Password" type="password" name="password"
                                id="password">
                            <div class="pass-icon" id="passwordToggle"><i class="fa-sharp fa-light fa-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-person-lines-fill"></i> Contact Information</h4>
        </div>
        <div class="card-body">

            <h5 class="mb-2">Spouse Details (If Married) </h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="spouse_surname_name">Surname</label>
                    <input type="text" class="form-control" id="spouse_surname_name" name="spouse_surname_name"
                        placeholder="Surname" value="{{ $employee->spouse_surname_name ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="spouse_first_name">First Name</label>
                    <input type="text" class="form-control" id="spouse_first_name" name="spouse_first_name"
                        placeholder="First Name" value="{{ $employee->spouse_first_name ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label for="spouse_middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="spouse_middle_name" name="spouse_middle_name"
                        placeholder="Middle Name" value="{{ $employee->spouse_middle_name ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="spouse_date_of_birth">Date of Birth</label>
                    <input type="text" class="form-control datepicker" id="spouse_date_of_birth"
                        name="spouse_date_of_birth" placeholder="Date of Birth"
                        value="{{ $employee->spouse_date_of_birth ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="spouse_national_id">ID Number</label>
                    <input type="text" class="form-control" id="spouse_national_id" name="spouse_national_id"
                        placeholder="National ID" value="{{ $employee->spouse_national_id ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="phone2">Contact Phone</label>
                    <input type="text" class="phone-input-control" id="phone2" name="spouse_phone"
                        value="{{ $employee->spouse_phone ?? '' }}">
                    <input type="text" hidden id="code2" name="spouse_phone_code"
                        value="{{ $employee->spouse_phone_code ?? '' }}">
                    <input type="text" hidden id="country2" name="spouse_phone_country"
                        value="{{ $employee->spouse_phone_country ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="spouse_current_employer">Current Employer</label>
                    <input type="text" class="form-control" id="spouse_current_employer"
                        name="spouse_current_employer" placeholder="Current Employer"
                        value="{{ $employee->spouse_current_employer ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label for="spouse_postal_address">Postal Address</label>
                    <input type="text" class="form-control" id="spouse_postal_address"
                        name="spouse_postal_address" placeholder="Postal Address"
                        value="{{ $employee->spouse_postal_address ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label for="spouse_physical_address">Physical Address</label>
                    <input type="text" class="form-control" id="spouse_physical_address"
                        name="spouse_physical_address" placeholder="Physical Address"
                        value="{{ $employee->spouse_physical_address ?? '' }}">
                </div>
            </div>

            <h5 class="mb-2">Emergency Contact</h5>
            @for ($i = 1; $i <= 2; $i++)
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="emmergency_contact_name_{{ $i }}">Name</label>
                        <input type="text" class="form-control" id="emmergency_contact_name_{{ $i }}"
                            name="emmergency_contact_name[]" placeholder="Name"
                            value="{{ $employee->emmergency_contact_name[$i - 1] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="emmergency_contact_relationship_{{ $i }}">Relationship</label>
                        <input type="text" class="form-control"
                            id="emmergency_contact_relationship_{{ $i }}"
                            name="emmergency_contact_relationship[]" placeholder="e.g. Father / Wife / Brother etc"
                            value="{{ $employee->emmergency_contact_relationship[$i - 1] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="emmergency_contact_address_{{ $i }}">Contact Address</label>
                        <input type="text" class="form-control"
                            id="emmergency_contact_address_{{ $i }}" name="emmergency_contact_address[]"
                            placeholder="Contact Address"
                            value="{{ $employee->emmergency_contact_address[$i - 1] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="phone{{ 2 + $i }}">Contact Phone</label>
                        <input type="text" class="phone-input-control" id="phone{{ 2 + $i }}"
                            name="emmergency_contact_phone[]"
                            value="{{ $employee->emmergency_contact_phone[$i - 1] ?? '' }}">
                        <input type="text" hidden id="code{{ 2 + $i }}"
                            name="emmergency_contact_phone_code[]"
                            value="{{ $employee->emmergency_contact_phone_code[$i - 1] ?? '' }}">
                        <input type="text" hidden id="country{{ 2 + $i }}"
                            name="emmergency_contact_phone_country[]"
                            value="{{ $employee->emmergency_contact_phone_country[$i - 1] ?? '' }}">
                    </div>
                </div>
            @endfor

        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
