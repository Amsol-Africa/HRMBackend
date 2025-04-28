<?php

namespace App\Exports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyResponsesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function collection()
    {
        return $this->survey->responses()->with(['answers', 'answers.question', 'answers.option'])->get();
    }

    public function headings(): array
    {
        $questions = $this->survey->questions()->pluck('question_text')->toArray();
        return array_merge(
            ['Response ID', 'Submitted At', 'User ID', 'Is Anonymous'],
            $questions
        );
    }

    public function map($response): array
    {
        $answers = [];
        foreach ($this->survey->questions as $question) {
            $answer = $response->answers->where('survey_question_id', $question->id)->first();
            $answers[] = $answer
                ? ($answer->option ? $answer->option->option_text : ($answer->answer_text ?? 'N/A'))
                : 'N/A';
        }

        return array_merge(
            [
                $response->id,
                $response->submitted_at->format('Y-m-d H:i:s'),
                $response->user_id ?? 'N/A',
                $response->is_anonymous ? 'Yes' : 'No',
            ],
            $answers
        );
    }
}
