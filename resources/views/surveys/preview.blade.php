<x-app-layout title="Survey Preview">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="mb-0">Preview: {{ $survey->title }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $survey->description ?? 'No description provided.' }}</p>
                    <form>
                        @foreach($survey->questions as $question)
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                <span class="text-danger">*</span>
                                @endif
                            </label>
                            @if($question->question_type === 'text')
                            <input type="text" class="form-control" disabled>
                            @elseif($question->question_type === 'textarea')
                            <textarea class="form-control" rows="3" disabled></textarea>
                            @elseif($question->question_type === 'multiple_choice')
                            <div>
                                @foreach($question->options as $option)
                                <div class="form-check">
                                    <input type="radio" name="question_{{ $question->id }}" class="form-check-input"
                                        disabled>
                                    <label class="form-check-label">{{ $option->option_text }}</label>
                                </div>
                                @endforeach
                            </div>
                            @elseif($question->question_type === 'rating')
                            <div>
                                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                                    <input type="radio" name="question_{{ $question->id }}" class="form-check-input"
                                        disabled>
                                    <label class="form-check-label">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        @endif
                </div>
                @endforeach
                <div class="mt-3">
                    <a href="{{ route('business.surveys.show', [$businessSlug, $survey->id]) }}"
                        class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>