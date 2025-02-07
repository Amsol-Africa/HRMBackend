<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class Advance extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'employee_id',
        'amount',
        'date',
        'note',
    ];

    /**
     * Get the employee that owns the Advance.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
