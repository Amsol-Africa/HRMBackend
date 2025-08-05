<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory, HasSlug, LogsActivity;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
        'requires_approval',
        'is_paid',
        'allowance_accruable',
        'allows_half_day',
        'requires_attachment',
        'max_continuous_days',
        'min_notice_days',
        'is_active',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_paid' => 'boolean',
        'allowance_accruable' => 'boolean',
        'allows_half_day' => 'boolean',
        'requires_attachment' => 'boolean',
        'max_continuous_days' => 'integer',
        'min_notice_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    /**
     * Relationship to the Business model.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Relationship to the LeavePolicy model.
     */
    public function leavePolicies()
    {
        return $this->hasMany(LeavePolicy::class);
    }
}
