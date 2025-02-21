

@if(count(value: $taskprogresses))
    @foreach ($taskprogresses as $progress)
        <div class="col-md-4">
            <x-task-progress :progress="$progress" />
        </div>
    @endforeach
@else
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center"
                style="height: 200px; display: flex; justify-content: center; align-items: center;">
                <h5 class="card-text">No progress updates at the moment.</h5>
            </div>
        </div>
    </div>
@endif
