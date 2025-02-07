<?php

namespace App\Models;

use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'employee_id',
        'amount',
        'interest_rate',
        'term_months',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'employee_id' => 'integer',
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'term_months' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}
