<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    public function applicants()
    {
        return $this->belongsToMany(Applicant::class)->withPivot('skill_level');
    }

    // Example: scope skills by name (for searching, etc.)
    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where('name', 'like', "%$searchTerm%");
        }
        return $query;
    }
}
