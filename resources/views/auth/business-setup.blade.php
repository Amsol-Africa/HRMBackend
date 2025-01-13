<x-setup-layout>

    <div class="authentication-inner row">

        <div class="d-none d-md-flex col-lg-6 col-md-6 p-0">
            <div class="authentication-image d-flex justify-content-center align-items-center">
                <img src="/assets/images/sign/sign-up.png" alt="image">
            </div>
        </div>

        <div class="d-flex col-lg-6 col-md-6 col-12 align-items-center">
            <div class="card__wrapper">
                <div class="authentication-top text-center mb-20">
                    <a href="javascript:;" class="authentication-logo logo-black">
                        <img src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    <a href="javascript:;" class="authentication-logo logo-white">
                        <img src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    <h4 class="mb-15">Set Up Your HRM</h4>
                    <p class="mb-15">Greate you are here, set up your organization. It's the first step to managing your HR processes efficiently and effectively.</p>
                </div>

                <form class="" id="hrmSetupForm">

                    @csrf

                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="name">Company / Organization name</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Company / organization Name" name="name" id="name" :value="old('name')" type="text" required autocomplete="name">
                        </div>
                    </div>
                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="size">Company size</label>
                        </div>
                        <div class="form__input">
                            <select id="company_size" name="company_size" required class="form-select">
                                <option value="">Select Company Size</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200">51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="500+">500+ employees</option>
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
                                <option value="technology">Technology</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="manufacturing">Manufacturing</option>
                                <option value="retail">Retail</option>
                                <option value="services">Services</option>
                            </select>
                        </div>
                    </div>
                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="phone">Contact phone</label>
                        </div>
                        <div class="form__input">
                            <input class="phone-input-control" name="phone" id="phone" type="text" required autocomplete="phone">
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
                        <a class="btn btn-primary w-100" href="{{ route('setup.modules') }}"> Complete Setup <i class="ms-2 bi bi-check-circle"></i> </a>
                        {{-- <button class="btn btn-primary w-100" onclick="setup(this)" type="button"> Complete Setup <i class="ms-2 bi bi-check-circle"></i> </button> --}}
                    </div>
                </form>

            </div>
        </div>

    </div>

</x-setup-layout>
