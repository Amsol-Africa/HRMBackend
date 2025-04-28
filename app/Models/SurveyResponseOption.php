<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponseOption extends Model
{
    protected $fillable = [
        'survey_question_id',
        'option_text',
        'order',
    ];

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}