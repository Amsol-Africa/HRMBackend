<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLinkVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'short_link_id',
        'ip_address',
        'user_agent',
        'browser',
        'os',
        'device_type',
        'country'
    ];

    public function shortLink()
    {
        return $this->belongsTo(ShortLink::class);
    }
}
