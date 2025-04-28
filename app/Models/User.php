<?php

namespace App\Models;

use App\Models\Client;
use App\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use App\Notifications\TwoFactorCodeNotification;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, HasStatuses, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'country',
        'code',
        'provider',
        'provider_token',
        'social_id',
        'email_verified_at',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars');
    }
    public function getImageUrl()
    {
        $media = $this->getFirstMedia('avatars');
        if ($media && File::exists($media->getPath())) {
            return $media->getUrl();
        }
        return asset('media/avatar.png');
    }

    //rships
    public function business()
    {
        return $this->hasOne(Business::class);
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }

    public function managedClients()
    {
        return $this->hasMany(Client::class, 'employee_id');
    }
    public function businessesAsManager()
    {
        return $this->hasManyThrough(Business::class, Client::class, 'employee_id', 'id', 'id', 'client_business');
    }
    public function clientBusinesses()
    {
        return $this->hasManyThrough(Client::class, Business::class, 'user_id', 'business_id', 'id', 'id');
    }
    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function getAvailableContexts()
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function setActiveContext($role)
    {
        if ($this->hasRole($role)) {
            session(['active_role' => $role]);
            return true;
        }
        return false;
    }

    public function getActiveContext()
    {
        return session('active_role', null);
    }

    public function requiresTwoFactorAuthentication(): bool
    {
        return $this->hasAnyRole(['business-admin', 'business-hr', 'business-finance']);
    }

    public function generateTwoFactorCode(): void
    {
        // Delete any existing codes for the user
        \DB::table('two_factor_codes')->where('user_id', $this->id)->delete();

        // Generate a 6-digit code
        $code = Str::random(6, '0123456789');

        // Store the code
        \DB::table('two_factor_codes')->insert([
            'user_id' => $this->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send the code via email
        $this->notify(new TwoFactorCodeNotification($code));
    }

    public function verifyTwoFactorCode(string $code): bool
    {
        $record = \DB::table('two_factor_codes')
            ->where('user_id', $this->id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return false;
        }

        if ($record->attempts >= 3) {
            \DB::table('two_factor_codes')->where('user_id', $this->id)->delete();
            return false;
        }

        if ($record->code === $code) {
            \DB::table('two_factor_codes')->where('user_id', $this->id)->delete();
            return true;
        }

        \DB::table('two_factor_codes')
            ->where('user_id', $this->id)
            ->increment('attempts');

        return false;
    }
}
