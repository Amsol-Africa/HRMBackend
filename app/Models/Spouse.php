<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    protected $fillable = [
        'employee_id',
        'surname',
        'first_name',
        'middle_name',
        'date_of_birth',
        'national_id',
        'current_employer',
        'spouse_contact',
        'spouse_postal_address',
        'spouse_physical_address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
