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
                    <h4 class="mb-15">{{ config('app.name') }}</h4>
                    <p class="mb-15">Log in to continue.</p>
                </div>
                <form class="" id="loginForm">
                    @csrf
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
                        <div class="form__input-title d-flex justify-content-between">
                            <label for="password">Password</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control password" placeholder="Password" type="password" name="password"
                                required id="password">
                            <div class="pass-icon" id="password_toggle">
                                <i class="fa-sharp fa-light fa-eye-slash"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary w-100" type="button" onclick="login(this)">
                            <i class="bi bi-check-circle me-1"></i> Login
                        </button>
                    </div>
                </form>
                <p class="text-center mt-3">
                    <span>Don't have an account?</span>
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">
                        <span>Get started</span>
                    </a>
                </p>
                <p class="text-center mt-2">
                    <a href="{{ route('password.request') }}" class="text-indigo-600 hover:underline">
                        Forgot your password?
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-auth-layout>