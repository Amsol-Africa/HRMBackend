<x-app-layout>

    <div class="breadcrumb__area">
        <div class="breadcrumb__wrapper mb-25">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('business.index', $currentBusiness->slug) }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <form action="" id="businessDetailsForm">

                <input type="text" value="{{ $currentBusiness->slug }}" hidden name="business_slug" id="business_slug">

                <card class="mb-3">
                    <div class="card-body">

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="name">Company / Organization name</label>
                            </div>
                            <div class="form__input">
                                <input class="form-control" placeholder="Company / organization Name" name="name" id="name" value="{{ $currentBusiness->company_name }}" type="text" required autocomplete="name">
                            </div>
                        </div>

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="company_size">Company size</label>
                            </div>
                            <div class="form__input">
                                <select id="company_size" name="company_size" required class="form-select">
                                    <option value="">Select Company Size</option>
                                    <option value="1-10" {{ $currentBusiness->company_size == '1-10' ? 'selected' : '' }}>1-10 employees</option>
                                    <option value="11-50" {{ $currentBusiness->company_size == '11-50' ? 'selected' : '' }}>11-50 employees</option>
                                    <option value="51-200" {{ $currentBusiness->company_size == '51-200' ? 'selected' : '' }}>51-200 employees</option>
                                    <option value="201-500" {{ $currentBusiness->company_size == '201-500' ? 'selected' : '' }}>201-500 employees</option>
                                    <option value="500+" {{ $currentBusiness->company_size == '500+' ? 'selected' : '' }}>500+ employees</option>
                                </select>
                            </div>
                        </div>

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="industry">Industry</label>
                            </div>
                            <div class="form__input">
                                <select id="industry" name="industry" required class="form-select">
                                    <option value="">Select Industry</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry->slug }}"
                                            {{ $industry->slug === $currentBusiness->industry ? 'selected' : '' }}>
                                            {{ $industry->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="phone0">Contact phone</label>
                            </div>
                            <div class="form__input">
                                <input class="phone-input-control" name="phone" id="phone0" value="{{ $currentBusiness->phone }}" type="text" required autocomplete="phone">
                                <input name="code" hidden id="code0" type="text" required value="{{ $currentBusiness->code }}" autocomplete="code">
                                <input name="country" hidden id="country0" type="text" required value="{{ $currentBusiness->country }}" autocomplete="country">
                            </div>
                        </div>

                        <div class="row mb-3">

                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="registration_no">Registration No.</label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" placeholder="Company Registration No" name="registration_no" id="registration_no" value="" type="text" required autocomplete="registration_no">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="logo">Upload registration certificate </label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" type="file" name="logo" required id="logo">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="business_license_no">Business License Number</label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" placeholder="Business License Number" name="business_license_no" id="business_license_no" value="" type="text" required autocomplete="business_license_no">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="business_license_certificate">Upload Business License Certificate</label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" type="file" name="business_license_certificate" required id="business_license_certificate">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="physical_address">Physical Address</label>
                            </div>
                            <div class="form__input">
                                <input class="form-control" placeholder="Building, Street, Town" name="physical_address" id="physical_address" value="" type="text" required autocomplete="physical_address">
                            </div>
                        </div>


                        <div class="row mb-3">

                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="tax_pin_no">Tax Pin No.</label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" placeholder="Tax Pin No" name="tax_pin_no" id="tax_pin_no" value="" type="text" required autocomplete="tax_pin_no">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="from__input-box">
                                    <div class="form__input-title">
                                        <label for="tax_pin_certificate">Tax Pin Certificate </label>
                                    </div>
                                    <div class="form__input">
                                        <input class="form-control" type="file" name="tax_pin_certificate" required id="tax_pin_certificate">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="from__input-box">
                            <div class="form__input-title">
                                <label for="logo">Upload your logo (Optional) </label>
                            </div>
                            <div class="form__input">
                                <input class="form-control" type="file" name="logo" required id="logo">
                            </div>
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-primary w-100" onclick="updateBusiness(this)" type="button"> Complete Setup <i class="ms-2 bi bi-check-circle"></i> </button>
                        </div>

                    </div>
                </card>

            </form>
        </div>
        <div class="col-md-2">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div style="height: 200px; width: 200px">
                        <img src="{{ $currentBusiness->getImageUrl() }}" alt="{{ $currentBusiness->company_name }}" class="img-fluid">
                    </div>
                </div>
            </div>

            <!-- Registration Certificate (Non-image file) -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <a href="{{ $currentBusiness->getFirstMediaUrl('registration_certificates') }}" target="_blank">
                        Registration Certificate
                    </a>
                </div>
            </div>

            <!-- Tax Pin Certificate (Non-image file) -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <a href="{{ $currentBusiness->getFirstMediaUrl('tax_pin_certificates') }}" target="_blank">
                        Tax Pin Certificate
                    </a>
                </div>
            </div>

            <!-- Business License Certificate (Non-image file) -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ $currentBusiness->getFirstMediaUrl('business_license_certificates') }}" target="_blank">
                        Business License Certificate
                    </a>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/js/main/businesses.js') }}" type="module"></script>
    @endpush

</x-app-layout>
