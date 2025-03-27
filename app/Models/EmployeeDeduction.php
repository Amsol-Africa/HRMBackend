<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDeduction extends Model
{
    use HasFactory, HasStatuses, LogsActivity;

    protected $table = 'employee_deductions';

    protected $fillable = ['employee_id', 'deduction_id', 'amount', 'rate', 'is_active'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }
}