<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['interview_id', 'interviewer_id', 'comments', 'score', 'recommendation'];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
