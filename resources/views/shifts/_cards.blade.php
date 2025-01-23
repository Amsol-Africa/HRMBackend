<div class="row g-2">
    @foreach ($shifts as $shift)
        <div class="col-md-4">
            <x-shift-card :shift="$shift" />
        </div>
    @endforeach
</div>
