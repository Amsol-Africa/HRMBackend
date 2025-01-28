<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicQualification extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'institution_name',
        'certification_obtained',
    ];
}
