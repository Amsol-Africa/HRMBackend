<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanRepayment extends Model
{
    use HasFactory, HasStatuses, LogsActivity;

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
