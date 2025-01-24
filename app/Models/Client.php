<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['business_id', 'client_business', 'employee_id'];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function managedBusiness()
    {
        return $this->belongsTo(Business::class, 'client_business');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
