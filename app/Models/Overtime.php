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
        'description',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'overtime_hours' => 'decimal:2',
        'rate' => 'decimal:2',
        'total_pay' => 'decimal:2',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
