<div class="row">
    <div class="col-xxl-7">
        <div class="card__wrapper height-equal">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Personal Information</h5>
                    <a data-bs-target="#profile__info" data-bs-toggle="modal" class="edit-icon" href="#">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="profile-view d-flex flex-wrap justify-content-between align-items-start">
                    <div class="d-flex flex-wrap align-items-start gap-20">
                        <div class="profile-img-wrap">
                            <div class="profile-img">
                                <a href="#"><img src="{{ $user->getImageUrl() }}" alt="User Image"></a>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h3 class="user-name mb-15">{{ $user->name }}</h3>
                            <h6 class="text-muted mb-5">{{ $user->employee->department->name }}</h6>
                            <span class="d-block text-muted mb-5">Web Designer</span>
                            <h6 class="small employee-id text-black mb-5">Employee ID : {{ $user->employee->employee_code }}</h6>
                            <span class="d-block text-muted mb-20">Employemnt Date : {{ date("jS, M, Y", strtotime($user->employee->employment_date)) }} </span>
                            <div class="employee-msg"><a class="btn btn-primary" href="">Send Message</a></div>
                        </div>
                    </div>
                    <div class="personal-info-wrapper pr-20">
                        <ul class="personal-info">
                            <li>
                                <div class="title">Phone:</div>
                                <div class="text text-link-hover"><a href="tel:{{ $user->phone }}"> {{ $user->phone }} </a></div>
                            </li>
                            <li>
                                <div class="title">Email:</div>
                                <div class="text text-link-hover">{{ $user->email }}</div>
                            </li>
                            <li>
                                <div class="title">Birthday:</div>
                                <div class="text">{{ date("jS, M, Y", strtotime($user->employee->date_of_birth)) }}</div>
                            </li>
                            <li>
                                <div class="title">Address:</div>
                                <div class="text">{{$user->employee->address}}</div>
                            </li>
                            <li>
                                <div class="title">Gender:</div>
                                <div class="text">{{ ucfirst($user->employee->gender) }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-5">
        <div class="card__wrapper height-equal">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Emergency Contact</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#emergency_contact_modal">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="row">
                    @foreach ($user->employee->emergencyContacts as $key => $emergency_contact)
                        <div class="col-6">
                            <div class="emergency-contact">
                                <h6 class="card__sub-title mb-10"> @if ($key == 0) Primary Contact @else Secondary Contact @endif </h6>
                                <ul class="personal-info">

                                    <li>
                                        <div class="title">Name:</div>
                                        <div class="text">{{ $emergency_contact->name }}</div>
                                    </li>
                                    <li>
                                        <div class="title">Relationship:</div>
                                        <div class="text">{{ $emergency_contact->relationship }}</div>
                                    </li>
                                    <li>
                                        <div class="title">Phone:</div>
                                        <div class="text text-link-hover"><a href="tel:{{ $emergency_contact->telephone }}">{{ $emergency_contact->telephone }}</a>, <a href="tel:9876543210">9876543210</a></div>
                                    </li>
                                    <li>
                                        <div class="title">Address:</div>
                                        <div class="text text-link-hover"> {{ $emergency_contact->contact_address }} </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6">
        <div class="card__wrapper">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Academic Qualification</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#education__info">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="education__box">
                    <ul class="education__list">
                        @foreach ($user->employee->academicDetails as $qualification)
                            <li>
                                <div class="education__user">
                                    <div class="before__circle"></div>
                                </div>
                                <div class="education__content">
                                    <div class="timeline-content">
                                        <a href="#" class="name">{{ $qualification->institution_name }}</a>
                                        <span class="degree">{{ $qualification->certification_obtained }}</span>
                                        <span class="year">{{ date("Y", strtotime($qualification->start_date)) }} - {{ date("Y", strtotime($qualification->end_date)) }} </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6">
        <div class="card__wrapper">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Previous Employment</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#experience__info">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="education__box">
                    <ul class="education__list">
                        <li>
                            <div class="education__user">
                                <div class="before__circle"></div>
                            </div>
                            <div class="education__content">
                                <div class="timeline-content">
                                    <a href="#" class="name">Employer: {{ $user->employee->previousEmployment->employer_name }}</a>
                                    <a href="#" class="name">Business / Profession: {{ $user->employee->previousEmployment->business_or_profession }}</a>
                                    <a href="#" class="name">Address Location: {{ $user->employee->previousEmployment->address }}</a>
                                    <span class="name">Capacity Employed: {{ $user->employee->previousEmployment->capacity_employed }}</span>
                                    <span class="name">Dates: {{ date("Y", strtotime($user->employee->previousEmployment->start_date)) }} - {{ date("Y", strtotime($user->employee->previousEmployment->end_date)) }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="card__wrapper">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Bank Account</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#bank__account__info">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="personal-info-wrapper bank__account">
                    <ul class="personal-info">
                        <li>
                            <div class="title">Account Holder Name:</div>
                            <div class="text">{{ $user->employee->paymentDetails->account_name }}</div>
                        </li>
                        <li>
                            <div class="title">Account Number:</div>
                            <div class="text">{{ $user->employee->paymentDetails->account_number }}</div>
                        </li>
                        <li>
                            <div class="title">Bank Name:</div>
                            <div class="text">{{ $user->employee->paymentDetails->bank_name }}</div>
                        </li>
                        <li>
                            <div class="title">Branch Name:</div>
                            <div class="text">{{ $user->employee->paymentDetails->bank_branch }}</div>
                        </li>
                        <li>
                            <div class="title">Branch Code:</div>
                            <div class="text">{{ $user->employee->paymentDetails->bank_branch_code }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="card__wrapper">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Passport Information</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#passport__info">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="personal-info-wrapper bank__account">
                    <ul class="personal-info">
                        <li>
                            <div class="title">National ID:</div>
                            <div class="text">{{ $user->employee->national_id }}</div>
                        </li>
                        <li>
                            <div class="title">Passport Number:</div>
                            <div class="text">{{ $user->employee->passport_no }}</div>
                        </li>
                        <li>
                            <div class="title">Nationality:</div>
                            <div class="text">{{ $user->country }}</div>
                        </li>
                        <li>
                            <div class="title">Issue Date:</div>
                            <div class="text">{{ date("jS, Y, M", strtotime($user->employee->passport_issue_date)) }}</div>
                        </li>
                        <li>
                            <div class="title">Expiry Date:</div>
                            <div class="text">{{ date("jS, Y, M", strtotime($user->employee->passport_expiry_date)) }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4">
        <div class="card__wrapper">
            <div class="employee__profile-single-box p-relative">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-15">
                    <h5 class="card__heading-title">Documents</h5>
                    <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#social__info">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                </div>
                <div class="personal-info-wrapper bank__account">
                    <ul class="personal-info">
                        <li>
                            <div class="title">Curriculum Vitae:</div>
                            <div class="text text-link-hover">
                                @if ($user->employee->getFirstMediaUrl('cv_attachments'))
                                    <a href="{{ $user->employee->getFirstMediaUrl('cv_attachments') }}" target="_blank">Download CV</a>
                                @else
                                    <span>No CV uploaded</span>
                                @endif
                            </div>
                        </li>
                        <li>
                            <div class="title">Academic Documents:</div>
                            <div class="text text-link-hover">
                                @if ($user->employee->getMedia('academic_files')->count())
                                    @foreach ($user->employee->getMedia('academic_files') as $file)
                                        <a href="{{ $file->getUrl() }}" target="_blank">{{ $file->name }}</a><br>
                                    @endforeach
                                @else
                                    <span>No academic documents uploaded</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
