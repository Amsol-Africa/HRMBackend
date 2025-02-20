<div class="card mb-3">
    <div class="card-header">
        <h5 class="">{{ __('Update Password') }}</h5>
        <p>{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </div>
    <div class="card-body mb-0">

        <form method="post" id="changePasswordForm">
            @csrf

            <div class="form-group">
                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            </div>

            <div class="form-group">
                <x-input-label for="update_password_password" :value="__('New Password')" />
                <x-text-input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
            </div>

            <div class="form-group">
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            </div>

            <div class="">
                <x-primary-button onclick="changePassword(this)" class="w-100"> <i class="bi bi-check-circle me-2"></i> {{ __('Save') }}</x-primary-button>
            </div>
        </form>

    </div>
</div>
