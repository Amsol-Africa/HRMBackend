<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;
use App\Models\InterviewFeedback;

class InterviewFeedbackController extends Controller
{
    public function store(Request $request, Interview $interview)
    {
        $request->validate([
            'comments' => 'required|string',
            'score' => 'required|integer|min:1|max:10',
            'recommendation' => 'required|in:hire,reject,second_interview',
        ]);

        $feedback = InterviewFeedback::updateOrCreate(
            ['interview_id' => $interview->id],
            [
                'hr_id' => auth()->id(),
                'comments' => $request->comments,
                'score' => $request->score,
                'recommendation' => $request->recommendation,
            ]
        );

        return response()->json(['message' => 'Feedback submitted successfully', 'feedback' => $feedback]);
    }
}
