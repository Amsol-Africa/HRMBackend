<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class EmployeeAllowance extends Model
{
    use LogsActivity, HasStatuses;
    protected $fillable = [
        'employee_id',
        'allowance_id',
        'amount',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }
}