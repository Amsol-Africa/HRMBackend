<x-auth-layout>

    <div class="authentication-wrapper basic-authentication">
        <div class="authentication-inner">
            <div class="card__wrapper">

                <div class="authentication-top text-center mb-20">
                    <a href="javascript:;" class="authentication-logo logo-black">
                        <img src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    <a href="javascript:;" class="authentication-logo logo-white">
                        <img src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    <h4 class="mb-15">Welcome to {{ config('app.name') }}</h4>
                    <p class="mb-15">Please create your account to get started..!</p>
                </div>

                <form class="" id="registerForm">

                    @csrf

                    @if (isset($registration_token) && !empty($registration_token))
                    <input type="text" hidden name="registration_token" value="{{ $registration_token }}">
                    @endif

                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="name">Full Name</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Full Name" name="name" id="name"
                                :value="old('name')" type="text" required autocomplete="name">
                        </div>
                    </div>
                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="email">Email</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Email" name="email" id="email" type="email"
                                required autocomplete="email">
                        </div>
                    </div>
                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="phone">Phone</label>
                        </div>
                        <div class="form__input">
                            <input class="phone-input-control" name="phone" id="phone" type="text" required
                                autocomplete="phone">
                            <input name="code" id="code" type="text" hidden required autocomplete="code">
                            <input name="country" id="country" type="text" hidden required autocomplete="country">
                        </div>
                    </div>
                    <div class="from__input-box">
                        <div class="form__input-title d-flex justify-content-between">
                            <label for="password">Password</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Password" type="password" name="password" required
                                id="password">
                            <div class="pass-icon" id="passwordToggle"><i class="fa-sharp fa-light fa-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary w-100" onclick="register(this)" type="button"> <i
                                class="bi bi-check-circle me-1"></i> Sign Up</button>
                    </div>
                </form>

                <p class="text-center">
                    <span>Have an account?</span>
                    <a href="{{ route('login') }}">
                        <span>Login</span>
                    </a>
                </p>

                <div class="divider mb-10 text-center">
                    <div class="divider-text">or</div>
                </div>

                <div class="common-social">
                    <a href="javascript:;"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="javascript:;"><i class="fa-brands fa-google"></i></a>
                </div>

            </div>
        </div>
    </div>

    <style>
    .iti {
        width: 100%;
    }

    .iti__country-name {
        display: inline-block !important;
        margin-left: 6px;
        color: #000;
        font-weight: 400;
        width: 275px;
    }
    </style>
</x-auth-layout>