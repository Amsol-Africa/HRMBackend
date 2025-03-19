<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'business_id',
        'issue_date',
        'reason',
        'description',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}