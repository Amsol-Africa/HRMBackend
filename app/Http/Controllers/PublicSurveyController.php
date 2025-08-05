<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyResponseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicSurveyController extends Controller
{
    public function show(Survey $survey)
    {
        if (!$this->canAccessSurvey($survey)) {
            return redirect()->back()->withErrors('You do not have permission to view this survey.');
        }

        $survey->load('questions.options');
        return view('surveys.public.show', compact('survey'));
    }

    public function submit(Request $request, Survey $survey)
    {
        if (!$this->canAccessSurvey($survey)) {
            return redirect()->back()->withErrors('You do not have permission to submit this survey.');
        }

        $validated = $request->validate([
            'is_anonymous' => 'boolean',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:survey_questions,id',
            'answers.*.answer_text' => 'required_if:answers.*.option_id,null|string|nullable',
            'answers.*.option_id' => 'nullable|exists:survey_question_options,id',
        ]);

        $requiredQuestions = $survey->questions()->where('is_required', true)->pluck('id')->toArray();
        $answeredQuestionIds = array_column($validated['answers'], 'question_id');
        $missingRequired = array_diff($requiredQuestions, $answeredQuestionIds);
        if (!empty($missingRequired)) {
            return back()->withErrors('Please answer all required questions.')->withInput();
        }

        return \DB::transaction(function () use ($survey, $validated) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'client_id' => Auth::check() && Auth::user()->business ? Auth::user()->business->id : null,
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'submitted_at' => now(),
            ]);

            foreach ($validated['answers'] as $answerData) {
                SurveyResponseAnswer::create([
                    'survey_response_id' => $response->id,
                    'survey_question_id' => $answerData['question_id'],
                    'survey_response_option_id' => $answerData['option_id'] ?? null,
                    'answer_text' => $answerData['answer_text'] ?? null,
                ]);
            }

            return redirect()->route('surveys.public.show', $survey->id)->with('success', 'Thank you for submitting the survey!');
        });
    }

    protected function canAccessSurvey(Survey $survey)
    {
        if ($survey->status !== 'active') {
            return false;
        }

        if ($survey->access_type === 'public') {
            return true;
        }

        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $business = $survey->business;

        return match ($survey->access_type) {
            'private' => $user->hasRole('business-admin', $business),
            'employee_only' => $user->hasRole('business-employee', $business),
            'client_only' => $user->business && $user->business->isClientOf($business),
            default => false,
        };
    }
}
