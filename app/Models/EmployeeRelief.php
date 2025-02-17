<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRelief extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'relief_id',
        'amount',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function relief()
    {
        return $this->belongsTo(Relief::class);
    }
}
