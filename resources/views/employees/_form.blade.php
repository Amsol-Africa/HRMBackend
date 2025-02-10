

<form id="employeesForm" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-person"></i> Bio Information</h4>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-12 mb-4">
                    <label for="gender">Choose location</label>
                    <select name="location" id="location" class="form-select">
                        <option>Choose location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->slug }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <p><i>Leave empty to add employee to main business</i></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="last_name">Surname</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="Last Name">
                </div>
                <div class="col-md-4">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="First Name">
                </div>
                <div class="col-md-4">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" required placeholder="Middle Name">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required placeholder="Personal e-mail Address">
                </div>
                <div class="col-md-3">
                    <label for="phone0">Phone</label>
                    <input type="text" class="phone-input-control" id="phone0" name="phone" required>
                    <input type="text" hidden id="code0" name="phone_code" required>
                    <input type="text" hidden id="country0" name="phone_country" required>
                </div>
                <div class="col-md-3">
                    <label for="phone1">Alternate Phone No.</label>
                    <input type="text" class="phone-input-control" id="phone1" name="alternate_phone" required>
                    <input type="text" hidden id="code1" name="alternate_phone_code" required>
                    <input type="text" hidden id="country1" name="alternate_phone_country" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="text" class="form-control datepicker" id="date_of_birth" name="date_of_birth" required placeholder="Date of Birth">
                </div>
                <div class="col-md-4">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" required placeholder="Place of Birth">
                </div>
                <div class="col-md-4">
                    <label for="marital_status">Marital Status</label>
                    <select name="marital_status" id="marital_status" class="form-select">
                        <option>-Select-</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="national_id">ID Number</label>
                    <input type="text" class="form-control" id="national_id" name="national_id" required placeholder="National ID">
                </div>
                <div class="col-md-3">
                    <label for="place_of_issue">Place of Issue</label>
                    <input type="text" class="form-control" id="place_of_issue" name="place_of_issue" required placeholder="Place of Issue">
                </div>
                <div class="col-md-3">
                    <label for="tax_no">Tax No.</label>
                    <input type="text" class="form-control" id="tax_no" name="tax_no" required placeholder="Tax No.">
                </div>
                <div class="col-md-3">
                    <label for="nhif_no">NHIF No.</label>
                    <input type="text" class="form-control" id="nhif_no" name="nhif_no" required placeholder="NHIF No.">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="nssf_no">NSSF No.</label>
                    <input type="text" class="form-control" id="nssf_no" name="nssf_no" required placeholder="NSSF No.">
                </div>
                <div class="col-md-3">
                    <label for="passport_no">Passport No.</label>
                    <input type="text" class="form-control" id="passport_no" name="passport_no" required placeholder="Passport No.">
                </div>
                <div class="col-md-3">
                    <label for="passport_issue_date">Passport Issue Date.</label>
                    <input type="text" class="form-control datepicker" id="passport_issue_date" name="passport_issue_date" required placeholder="Passport Issue Date">
                </div>
                <div class="col-md-3">
                    <label for="passport_expiry_date">Passport Expiry Date.</label>
                    <input type="text" class="form-control datepicker" id="passport_expiry_date" name="passport_expiry_date" required placeholder="Passport Expiry Date">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="address">Residential Address</label>
                    <input type="text" class="form-control" id="address" name="address" required placeholder="Residential Address">
                </div>
                <div class="col-md-4">
                    <label for="permanent_address">Permanent Residential Address</label>
                    <input type="text" class="form-control" id="permanent_address" name="permanent_address" required placeholder="Permanent Residential Address">
                </div>
                <div class="col-md-4">
                    <label for="blood_group">Blood Group</label>
                    <select name="blood_group" id="blood_group" class="form-select">
                        <option value="" selected disabled>Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="avatar">Profile Picture</label>
                    <input type="file" class="form-control" id="avatar" name="avatar" required>
                </div>
                <div class="col-md-6">
                    <div class="from__input-box">
                        <label for="password">Password <strong>(Leave empty if employee should not login)</strong> </label>
                        <div class="form__input">
                            <input class="form-control" placeholder="Password" type="password" name="password" required id="password">
                            <div class="pass-icon" id="passwordToggle"><i class="fa-sharp fa-light fa-eye-slash"></i></div>
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
                    <input type="text" class="form-control" id="spouse_surname_name" name="spouse_surname_name" required placeholder="Surname">
                </div>
                <div class="col-md-4">
                    <label for="spouse_first_name">First Name</label>
                    <input type="text" class="form-control" id="spouse_first_name" name="spouse_first_name" required placeholder="First Name">
                </div>
                <div class="col-md-4">
                    <label for="spouse_middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="spouse_middle_name" name="spouse_middle_name" required placeholder="Middle Name">
                </div>
                <div class="col-md-3">
                    <label for="spouse_date_of_birth">Date of Birth</label>
                    <input type="text" class="form-control datepicker" id="spouse_date_of_birth" name="spouse_date_of_birth" required placeholder="Date of Birth">
                </div>
                <div class="col-md-3">
                    <label for="spouse_national_id">ID Number</label>
                    <input type="text" class="form-control" id="spouse_national_id" name="spouse_national_id" required placeholder="National ID">
                </div>
                <div class="col-md-3">
                    <label for="phone2">Contact Phone</label>
                    <input type="text" class="phone-input-control" id="phone2" name="spouse_phone" required>
                    <input type="text" hidden id="code2" name="spouse_phone_code" required>
                    <input type="text" hidden id="country2" name="spouse_phone_country" required>
                </div>
                <div class="col-md-3">
                    <label for="spouse_current_employer">Current Employer</label>
                    <input type="text" class="form-control" id="spouse_current_employer" name="spouse_current_employer" required placeholder="Current Employer">
                </div>
                <div class="col-md-6">
                    <label for="spouse_postal_address">Postal Address</label>
                    <input type="text" class="form-control" id="spouse_postal_address" name="spouse_postal_address" required placeholder="Postal Address">
                </div>
                <div class="col-md-6">
                    <label for="spouse_physical_address">Physical Address</label>
                    <input type="text" class="form-control" id="spouse_physical_address" name="spouse_physical_address" required placeholder="Physical Address">
                </div>
            </div>

            <h5 class="mb-2">Emmergency Contact</h5>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="emmergency_contact_name_1">Name</label>
                    <input type="text" class="form-control" id="emmergency_contact_name_1" name="emmergency_contact_name[]" required placeholder="Name">
                </div>
                <div class="col-md-3">
                    <label for="emmergency_contact_relationship_1">Relationship</label>
                    <input type="text" class="form-control" id="emmergency_contact_relationship_1" name="emmergency_contact_relationship[]" required placeholder="e.g. Father / Wife / Brother etc">
                </div>
                <div class="col-md-3">
                    <label for="emmergency_contact_address_1">Contact Address</label>
                    <input type="text" class="form-control" id="emmergency_contact_address_1" name="emmergency_contact_address[]" required placeholder="Contact Address">
                </div>
                <div class="col-md-3">
                    <label for="phone3">Contact Phone</label>
                    <input type="text" class="phone-input-control" id="phone3" name="emmergency_contact_phone[]" required>
                    <input type="text" hidden id="code3" name="emmergency_contact_phone_code[]" required>
                    <input type="text" hidden id="country3" name="emmergency_contact_phone_country[]" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="emmergency_contact_name_2">Name</label>
                    <input type="text" class="form-control" id="emmergency_contact_name_2" name="emmergency_contact_name[]" required placeholder="Name">
                </div>
                <div class="col-md-3">
                    <label for="emmergency_contact_relationship_2">Relationship</label>
                    <input type="text" class="form-control" id="emmergency_contact_relationship_2" name="emmergency_contact_relationship[]" required placeholder="e.g. Father / Wife / Brother etc">
                </div>
                <div class="col-md-3">
                    <label for="emmergency_contact_address_2">Contact Address</label>
                    <input type="text" class="form-control" id="emmergency_contact_address_2" name="emmergency_contact_address[]" required placeholder="Contact Address">
                </div>
                <div class="col-md-3">
                    <label for="phone4">Contact Phone</label>
                    <input type="text" class="phone-input-control" id="phone4" name="emmergency_contact_phone[]" required>
                    <input type="text" hidden id="code4" name="emmergency_contact_phone_code[]" required>
                    <input type="text" hidden id="country4" name="emmergency_contact_phone_country[]" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="additional_emmergency_instructions">Additional Emergency Instructions</label>
                    <textarea name="additional_emmergency_instructions" class="form-control" placeholder="Additional Emergency Instructions" id="additional_emmergency_instructions"></textarea>
                </div>
            </div>

        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">
                <i class="bi bi-people"></i> Family Members
            </h4>
        </div>
        <div class="card-body">
            <h6>Starting with the most recent</h6>
            <div class="family-members-container">
                <div class="row mb-3 family-members-row">
                    <div class="col-md-4">
                        <label>Name</label>
                        <input type="text" class="form-control" name="family_member_name[]" required placeholder="Certification">
                    </div>
                    <div class="col-md-4">
                        <label>Relationship</label>
                        <input type="text" class="form-control" name="family_member_relationship[]" required placeholder="Institution">
                    </div>
                    <div class="col-md-4">
                        <label>Date of Birth</label>
                        <input type="text" class="form-control datepicker" name="family_member_date_of_birth[]" required placeholder="Date of Birth">
                    </div>
                    <div class="col-md-6">
                        <label>Contact Address</label>
                        <input type="text" class="form-control" name="family_member_contact_address[]" required placeholder="Contact Address">
                    </div>
                    <div class="col-md-6">
                        <label for="phone5">Contact Phone</label>
                        <input type="text" class="phone-input-control" id="phone5" name="family_member_contact_phone[]" required>
                        <input type="text" hidden id="code5" name="family_member_contact_phone_code[]" required>
                        <input type="text" hidden id="country5" name="family_member_contact_phone_country[]" required>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="button" id="addFamilyMemberDetails" class="btn btn-secondary">
                        <i class="bi bi-plus-circle"></i> Add More
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">
                <i class="bi bi-mortarboard"></i> Academic & Professional Details
            </h4>
        </div>
        <div class="card-body">
            <h6>Starting with the most recent</h6>
            <div class="academic-details-container">
                <div class="row mb-3 academic-details-row">
                    <div class="col-md-4">
                        <label>Certification Obtained</label>
                        <input type="text" class="form-control" name="certification[]" required placeholder="Certification">
                    </div>
                    <div class="col-md-4">
                        <label>Institution Name</label>
                        <input type="text" class="form-control" name="institution[]" required placeholder="Institution">
                    </div>
                    <div class="col-md-2">
                        <label>From</label>
                        <input type="text" class="form-control datepicker" name="from[]" required placeholder="From">
                    </div>
                    <div class="col-md-2">
                        <label>To</label>
                        <input type="text" class="form-control datepicker" name="to[]" required placeholder="To">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="button" id="addAcademicDetails" class="btn btn-secondary">
                        <i class="bi bi-plus-circle"></i> Add More
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-briefcase"></i> Previous Employment Details</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">

                <div class="col-md-4">
                    <label for="employer_name">Name of Employer</label>
                    <input type="text" class="form-control" name="employer_name" required placeholder="Name of Employer">
                </div>
                <div class="col-md-4">
                    <label for="business_or_profession">Business or Profession</label>
                    <input type="text" class="form-control" name="business_or_profession" required placeholder="Business or Profession">
                </div>
                <div class="col-md-4">
                    <label for="employment_address">Address</label>
                    <input type="text" class="form-control" name="employment_address" required placeholder="Address">
                </div>
                <div class="col-md-4">
                    <label for="employment_capacity">Capacity</label>
                    <input type="text" class="form-control" name="employment_capacity" required placeholder="Capacity">
                </div>
                <div class="col-md-4">
                    <label for="employment_from">From</label>
                    <input type="text" class="form-control datepicker" name="employment_from" required placeholder="From">
                </div>
                <div class="col-md-4">
                    <label for="employment_to">To</label>
                    <input type="text" class="form-control datepicker" name="employment_to" required placeholder="To">
                </div>
                <div class="col-md-12">
                    <label for="reason_for_leaving">Reason for leaving</label>
                    <textarea name="reason_for_leaving" class="form-control" rows="4" placeholder="Reason for leaving"></textarea>
                </div>

            </div>

        </div>
    </div>


    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-briefcase"></i> Employment Data</h4>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="employee_code">Employee Code</label>
                    <input type="text" class="form-control" id="employee_code" name="employee_code" required placeholder="e.g. EMP001">
                </div>

                <div class="col-md-4">
                    <label for="department">Department</label>
                    <select class="form-select" id="department" name="department" required>
                        <option value="" disabled selected>Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->slug }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="job_category">Job Category</label>
                    <select class="form-select" id="job_category" name="job_category" required>
                        <option value="" selected>Select Job Category</option>
                        @foreach($job_categories as $job_category)
                            <option value="{{ $job_category->slug }}">{{ $job_category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="employment_term">Employment Term.</label>
                    <select name="employment_term" id="employment_term" class="form-select">
                        <option value="">- Employment Status -</option>
                        <option value="contract">Contract</option>
                        <option value="fulltime">Full Time</option>
                        <option value="permanent">Permanent Employment</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="employment_date">Employment Start Date</label>
                    <input type="text" class="form-control datepicker" id="employment_date" name="employment_date" required placeholder="Employment Date">
                </div>
                <div class="col-md-4">
                    <label for="probation_end_date">Probabtion End Date.</label>
                    <input type="text" class="form-control datepicker" id="probation_end_date" name="probation_end_date" required placeholder="Probabtion End Date">
                </div>
                <div class="col-md-4">
                    <label for="contract_end_date">Contract End Date.</label>
                    <input type="text" class="form-control datepicker" id="contract_end_date" name="contract_end_date" required placeholder="Contract End Date">
                </div>
                <div class="col-md-4">
                    <label for="retirement_date">Retirement Date.</label>
                    <input type="text" class="form-control datepicker" id="retirement_date" name="retirement_date" required placeholder="Retirement Date">
                </div>
                <div class="col-md-4">
                    <label for="shift">Work Shift</label>
                    <select name="shift" id="shift" class="form-select">
                        <option value="">- Select shift -</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->slug }}"> {{ $shift->name }} </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <label for="job_description">Job Description</label>
                    <textarea class="form-control" id="job_description" name="job_description" rows="4" placeholder="Job Description..."></textarea>
                </div>
            </div>

        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-bank"></i> Salary & Payment Information</h4>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="basic_salary">Basic Salary</label>
                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" required placeholder="e.g. 50000">
                </div>
                <div class="col-md-4">
                    <label for="currency">Currency</label>
                    <select class="form-select" id="currency" name="currency" required>
                        <option value="KES">KES</option>
                        <option value="USD">USD</option>
                        <option value="TZS">TZS</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="payment_mode">Payment Mode</label>
                    <select class="form-select" id="payment_mode" name="payment_mode" required>
                        <option selected>Select Payment Mode</option>
                        <option value="bank">Bank</option>
                        <option value="Cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="mpesa">M-Pesa</option>
                    </select>
                </div>

            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="account_name">Account Name</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" required placeholder="Account Name">
                </div>
                <div class="col-md-4">
                    <label for="account_number">Account Number</label>
                    <input type="text" class="form-control" id="account_number" name="account_number" required placeholder="Account Number">
                </div>
                <div class="col-md-4">
                    <label for="bank_name">Bank Name.</label>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" required placeholder="Bank Name">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="bank_code">Bank Code.</label>
                    <input type="text" class="form-control" id="bank_code" name="bank_code" required placeholder="Bank Code">
                </div>
                <div class="col-md-4">
                    <label for="bank_branch">Branch.</label>
                    <input type="text" class="form-control" id="bank_branch" name="bank_branch" required placeholder="Branch">
                </div>
                <div class="col-md-4">
                    <label for="bank_branch_code">Branch Code.</label>
                    <input type="text" class="form-control" id="bank_branch_code" name="bank_branch_code" required placeholder="Branch Code">
                </div>
            </div>

        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title"> <i class="bi bi-folder"></i> Files & Attachments</h4>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="profile_picture">Professional Photo.</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
                </div>

                <div class="col-md-4">
                    <label for="cv_attachment">CV Attachments.</label>
                    <input type="file" class="form-control" id="cv_attachment" name="cv_attachment" required>
                </div>

                <div class="col-md-4">
                    <label for="academic_files">Accademic Files.</label>
                    <input type="file" multiple class="form-control" id="academic_files" name="academic_files" required>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="button" onclick="saveEmployee(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle"></i> Save Employee</button>
        </div>
    </div>

</form>
