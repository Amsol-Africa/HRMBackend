<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'is_required',
        'order',
    ];

    protected $casts = [
        'question_type' => 'string',
        'is_required' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function options()
    {
        return $this->hasMany(SurveyResponseOption::class)->orderBy('order');
    }

    public function answers()
    {
        return $this->hasMany(SurveyResponseAnswer::class);
    }
}