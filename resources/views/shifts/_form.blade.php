<form id="shiftsForm" method="post">
    @csrf
    @if(isset($shift))
        <input type="hidden" name="shift_slug" value="{{ $shift->slug }}">
    @endif

    <div class="form-group mb-3">
        <label for="shift_name">Shift Name</label>
        <input type="text" class="form-control" id="shift_name" name="shift_name" required placeholder="e.g Morning Shift" value="{{ isset($shift) ? $shift->name : old('shift_name') }}">
    </div>

    <div class="form-group mb-3">
        <label for="description">Shift Description</label>
        <textarea name="description" id="description" class="form-control" rows="4">{{ isset($shift) ? $shift->description : 'Short Description...' }}</textarea>
    </div>

    <div class="form-group mb-3">
        <label for="start_time">Start Time (24 HRS)</label>
        <input type="text" class="form-control" id="start_time" name="start_time" required placeholder="e.g 08:00" value="{{ isset($shift) ? $shift->start_time->format('H:i') : old('start_time') }}">
    </div>

    <div class="form-group mb-3">
        <label for="end_time">End Time (24 HRS) </label>
        <input type="text" class="form-control" id="end_time" name="end_time" required placeholder="e.g 16:00" value="{{ isset($shift) ? $shift->end_time->format('H:i') : old('end_time') }}">
    </div>

    <div>
        <button onclick="saveShift(this)" type="button" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> {{ isset($shift) ? 'Update Shift' : 'Save Shift' }}
        </button>
    </div>
</form>
