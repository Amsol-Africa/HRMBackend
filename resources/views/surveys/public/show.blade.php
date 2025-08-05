<x-app-layout title="{{ $survey->title }}">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white">
                    <h5 class="mb-0">{{ $survey->title }}</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @else
                    <p>{{ $survey->description ?? 'No description provided.' }}</p>
                    <form action="{{ route('surveys.submit', $survey->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_anonymous" value="1" class="form-check-input">
                                Submit Anonymously
                            </label>
                        </div>
                        @foreach($survey->questions as $index => $question)
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">
                            @if($question->question_type === 'text')
                            <input type="text" name="answers[{{ $index }}][answer_text]" class="form-control"
                                {{ $question->is_required ? 'required' : '' }}>
                            @elseif($question->question_type === 'textarea')
                            <textarea name="answers[{{ $index }}][answer_text]" class="form-control" rows="3"
                                {{ $question->is_required ? 'required' : '' }}></textarea>
                            @elseif($question->question_type === 'multiple_choice')
                            <div>
                                @foreach($question->options as $option)
                                <div class="form-check">
                                    <input type="radio" name="answers[{{ $index }}][option_id]"
                                        value="{{ $option->id }}" class="form-check-input"
                                        {{ $question->is_required ? 'required' : '' }}>
                                    <label class="form-check-label">{{ $option->option_text }}</label>
                                </div>
                                @endforeach
                            </div>
                            @elseif($question->question_type === 'rating')
                            <div>
                                @for($i = 1; $i <= 5; $i++) <div class="form-check form-check-inline">
                                    <input type="radio" name="answers[{{ $index }}][answer_text]" value="{{ $i }}"
                                        class="form-check-input" {{ $question->is_required ? 'required' : '' }}>
                                    <label class="form-check-label">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>
                        @endif
                </div>
                @endforeach
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Submit Survey</button>
                </div>
                </form>
                @endif
            </div>
        </div>
    </div>
    </div>
</x-app-layout>