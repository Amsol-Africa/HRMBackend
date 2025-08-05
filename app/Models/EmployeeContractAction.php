<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContractAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'employee_id',
        'action_type',
        'reason',
        'description',
        'action_date',
        'status',
        'issued_by_id',
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_id');
    }
}
