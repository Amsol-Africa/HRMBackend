<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class Overtime extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'employee_id',
        'business_id',
        'date',
        'overtime_hours',
        'rate',
        'total_pay',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
