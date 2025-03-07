<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class AcademicQualification extends Model
{
    use LogsActivity;
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'institution_name',
        'certification_obtained',
    ];
}
