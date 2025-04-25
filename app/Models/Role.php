<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'business_id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}