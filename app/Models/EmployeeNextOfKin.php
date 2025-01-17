<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeNextOfKin extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'phone',
        'code',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
