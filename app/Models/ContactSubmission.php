<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'country',
        'inquiry_type',
        'message',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }
}
