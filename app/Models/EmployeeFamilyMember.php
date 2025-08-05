<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeFamilyMember extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'date_of_birth',
        'contact_address',
        'phone',
        'code',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
