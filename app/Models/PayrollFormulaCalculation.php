<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollFormulaCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'payroll_formula_id',
        'input_amount',
        'result',
        'calculation_steps',
        'affected_fields',
    ];

    protected $casts = [
        'input_amount' => 'decimal:2',
        'result' => 'decimal:2',
        'calculation_steps' => 'array',
        'affected_fields' => 'array',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function formula()
    {
        return $this->belongsTo(PayrollFormula::class);
    }
}