<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class PreviousEmployment extends Model
{
    use LogsActivity;
    protected $fillable = [
        'employee_id',
        'employer_name',
        'business_or_profession',
        'address',
        'capacity_employed',
        'reason_for_leaving',
        'start_date',
        'end_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
