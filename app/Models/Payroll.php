<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class Payroll extends Model
{

    use HasStatuses;
    protected $fillable = [
        'business_id',
        'location_id',
        'payroll_type',
        'currency',
        'staff',
        'start_date',
        'end_date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employeePayrolls()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public static function getPayrolls($businessId)
    {
        return self::where('business_id', $businessId)->get();
    }

    public function getPayslips()
    {
        return $this->employeePayrolls;
    }
}
