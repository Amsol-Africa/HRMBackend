<x-app-layout title="{{ $survey->title }}">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-3">{{ $survey->title }}</h4>
                        @if($survey->description)
                        <p class="text-muted">{{ $survey->description }}</p. @endif @if(session('success')) <div
                                class="alert alert-success">
                            {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="publicSurveyForm" action="{{ route('surveys.public.submit', $survey->id) }}" method="POST"
                        class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_anonymous" value="1" class="form-check-input">
                                Submit anonymously
                            </label>
                        </div>

                        @foreach($survey->questions as $index => $question)
                        <div class="mb-4">
                            <label class="form-label fw-medium text-dark">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                <span class="text-danger">*</span>
                                @endif
                            </label>
                            @if($question->question_type === 'text')
                            <input type="text" name="answers[{{$index}}][answer_text]" class="form-control"
                                {{ $question->is_required ? 'required' : '' }}>
                            <input type="hidden" name="answers[{{$index}}][question_id]" value="{{ $question->id }}">
                            @elseif($question->question_type === 'textarea')
                            <textarea name="answers[{{$index}}][answer_text]" class="form-control" rows="4"
                                {{ $question->is_required ? 'required' : '' }}></textarea>
                            <input type="hidden" name="answers[{{$index}}][question_id]" value="{{ $question->id }}">
                            @elseif($question->question_type === 'multiple_choice')
                            @foreach($question->options as $option)
                            <div class="form-check">
                                <input type="radio" name="answers[{{$index}}][option_id]" value="{{ $option->id }}"
                                    class="form-check-input" {{ $question->is_required ? 'required' : '' }}>
                                <label class="form-check-label">{{ $option->option_text }}</label>
                            </div>
                            @endforeach
                            <input type="hidden" name="answers[{{$index}}][question_id]" value="{{ $question->id }}">
                            @elseif($question->question_type === 'rating')
                            <select name="answers[{{$index}}][answer_text]" class="form-select"
                                {{ $question->is_required ? 'required' : '' }}>
                                <option value="">Select rating</option>
                                @for($i = 1; $i <= 5; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                            <input type="hidden" name="answers[{{$index}}][question_id]" value="{{ $question->id }}">
                            @endif
                            @error("answers.{$index}.answer_text")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error("answers.{$index}.option_id")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary btn-modern">
                            <i class="fa fa-check me-2"></i> Submit Survey
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('publicSurveyForm');
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add('was-validated');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>