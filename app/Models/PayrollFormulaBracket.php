<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollFormulaBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_formula_id',
        'min',
        'max',
        'rate',
        'amount',
    ];

    public function formula()
    {
        return $this->belongsTo(PayrollFormula::class, 'payroll_formula_id');
    }
}
