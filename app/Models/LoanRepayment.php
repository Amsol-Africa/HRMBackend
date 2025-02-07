<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class LoanRepayment extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'loan_id',
        'repayment_date',
        'amount_paid',
        'notes',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
