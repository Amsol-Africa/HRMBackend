<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class Turnstile implements Rule
{
    public function passes($attribute, $value)
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        return $response->json('success') === true;
    }

    public function message()
    {
        return 'CAPTCHA verification failed.';
    }
}