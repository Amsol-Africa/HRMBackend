<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponseAnswer extends Model
{
    protected $fillable = [
        'survey_response_id',
        'survey_question_id',
        'survey_response_option_id',
        'answer_text',
    ];

    public function response()
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }

    public function option()
    {
        return $this->belongsTo(SurveyResponseOption::class);
    }
}