<x-auth-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />


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
                    <p class="mb-15">Log in to continue..!</p>
                </div>

                <form class="" id="loginForm">

                    @csrf

                    <div class="from__input-box">
                        <div class="form__input-title">
                            <label for="email">Email</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Email" name="email" id="email" type="email" required autocomplete="email">
                        </div>
                    </div>

                    <div class="from__input-box">
                        <div class="form__input-title d-flex justify-content-between">
                            <label for="passwordInput">Password</label>
                        </div>
                        <div class="form__input">
                            <input class="form-control" placeholder="Password" type="password" name="password" required id="passwordInput">
                            <div class="pass-icon" id="passwordToggle"><i class="fa-sharp fa-light fa-eye-slash"></i></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <a class="btn btn-primary w-100" href="{{ route('dashboard') }}"> <i class="bi bi-check-circle"></i> Login </a>
                        {{-- <button class="btn btn-primary w-100" type="button" onclick="login(this)"> <i class="bi bi-check-circle"></i> Login</button> --}}
                    </div>
                </form>

                <p class="text-center">
                    <span>Don't have an account?</span>
                    <a href="{{ route('register') }}">
                        <span>Get started</span>
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

</x-auth-layout>
