@forelse ($tasks as $task)
    <div class="col-md-4">
        <x-task-card :task="$task" />
    </div>
@empty
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center"
                style="height: 200px; display: flex; justify-content: center; align-items: center;">
                <h5 class="card-text">No tasks available at the moment.</h5>
            </div>
        </div>
    </div>
@endforelse
