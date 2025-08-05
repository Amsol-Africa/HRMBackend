<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use LogsActivity;
    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'contact_address',
        'telephone',
        'additional_instructions',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
