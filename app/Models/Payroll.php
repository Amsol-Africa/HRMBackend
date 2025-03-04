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
        'payrun_year',
        'payrun_month',
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

    public static function getPayrolls($col, $value)
    {
        return self::where($col, $value)->get();
    }

    public function getPayslips()
    {
        return $this->employeePayrolls;
    }
    public function getPayrollNameAttribute()
    {
        $monthName = date('M', mktime(0, 0, 0, $this->payrun_month, 1));
        return "Payroll - {$monthName} / {$this->payrun_year}";
    }

}
