<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiResult extends Model
{
    protected $fillable = [
        'kpi_id',
        'model_type',
        'model_id',
        'result_value',
        'meets_target',
        'measured_at',
    ];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}