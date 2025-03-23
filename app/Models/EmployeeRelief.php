<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeRelief extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'employee_id',
        'relief_id',
        'amount',
        'is_active',
        'start_date',
        'end_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function relief()
    {
        return $this->belongsTo(Relief::class);
    }
}