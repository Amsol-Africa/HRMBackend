<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeContactDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'work_phone',
        'work_phone_code',
        'work_email',
        'address',
        'city',
        'postal_code',
        'country',
        'email_signature'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
