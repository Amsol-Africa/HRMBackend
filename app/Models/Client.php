<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class Client extends Model
{
    use HasStatuses;
    protected $fillable = ['business_id', 'client_business', 'employee_id'];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function managedBusiness()
    {
        return $this->belongsTo(Business::class, 'client_business');
    }
    public function employee() //who manages the managed business, from another business that is not the managed business
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
