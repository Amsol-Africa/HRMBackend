<x-app-layout>
    <div class="row g-20">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body mb-0">

                    <form action="" id="updateProgressForm">

                        <input type="text" hidden name="task_slug" value="{{ $task->slug }}">

                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ isset($task) && $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ isset($task) && $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ isset($task) && $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description (optional)</label>
                            <textarea name="description" id="description" class="form-control" rows="7">Short description...</textarea>
                        </div>

                        <button class="btn btn-primary w-100" type="button" onclick="updateProgress(this)">
                            <i class="bi bi-check-circle me-2"></i> Update progress
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card__wrapper height-equal">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-20">
                    <h5 class="card__heading-title">Task progress</h5>
                </div>
                <ul class="timeline" id="timelinesContainer">

                    {{ loader() }}

                </ul>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/main/tasks.js') }}" type="module"></script>

        <script>

            $(document).ready(() => {
                timelines('{{ $task->slug }}')
            })

        </script>
    @endpush

</x-app-layout>
