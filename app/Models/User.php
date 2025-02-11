<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Client;
use Spatie\MediaLibrary\HasMedia;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, HasStatuses;

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
        return $this->hasManyThrough(Business::class,Client::class,'employee_id','id','id','client_business');
    }
    public function clientBusinesses()
    {
        return $this->hasManyThrough(Client::class,Business::class,'user_id','business_id','id','id');
    }
}
