<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'slug',
        'visits',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function visits()
    {
        return $this->hasMany(ShortLinkVisit::class);
    }
}
