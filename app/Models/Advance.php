<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Advance extends Model
{
    use HasFactory, HasStatuses, LogsActivity;

    protected $fillable = [
        'employee_id',
        'amount',
        'date',
        'note',
    ];

    // Add casts
    protected $casts = [
        'date' => 'date', // Casts to Carbon
        'amount' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the Advance.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}