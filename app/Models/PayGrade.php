<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayGrade extends Model
{
    protected $fillable = ['name', 'amount', 'job_category_id', 'department_id', 'business_id'];

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}