<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = ['ip_address', 'attempts', 'banned_until'];

    public function isBanned(): bool
    {
        return $this->banned_until && now()->lessThan($this->banned_until);
    }
}
