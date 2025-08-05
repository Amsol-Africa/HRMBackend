<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeePaymentDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'currency',
        'payment_mode',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'bank_branch',
        'bank_branch_code'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
