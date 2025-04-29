<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'target_url',
        'start_date',
        'end_date',
        'status',
        'has_survey',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function shortLink()
    {
        return $this->hasOne(ShortLink::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
