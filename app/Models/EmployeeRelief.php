<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRelief extends Model
{
    protected $fillable = ['employee_id', 'relief_id', 'amount', 'is_active', 'start_date', 'end_date'];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
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