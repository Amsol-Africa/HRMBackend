@foreach ($clockins as $clockin)

@php
    $employee = $clockin->employee;
@endphp
<div class="card mb-3 p-1">
    <div class="card-body mb-0 p-1">

        <div class="row g-20 align-items-center">
            <div class="col-md-10">
                <div class="card__title-wrap d-flex align-items-center justify-content-between">
                    <h5 class="card__heading-title">{{ $employee->user->name }}</h5>
                </div>
                <p class="text-dark mb-0">
                    Clock In:
                    @if($clockin->is_absent)
                        x Absent
                    @else
                        {{ $clockin->clock_in ? $clockin->clock_in->format('jS M H:i') : '-' }}
                    @endif
                </p>
                <p class="text-dark">Clock Out: {{ $clockin->clock_out ? $clockin->clock_out->format('jS M H:i') : '-' }}</p>
                <div class="d-flex align-items-center">
                    <div class="">
                        @if ($clockin->clock_out)
                        <button type="button" data-employee="{{ $employee->id }}" id="clock_out" class="btn btn-success"> <i class="bi me-2 bi-check-circle"></i> Clocked Out</button>
                        @else
                        <button type="button" onclick="clockOut(this)" data-employee="{{ $employee->id }}" id="clock_out" class="btn btn-danger"> <i class="bi me-2 bi-bell"></i> Clock Out</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <div style="height: 120px; width: 120px; overflow: hidden; border-radius: 100%">
                    <img style="height: 100%; width: 100%" src="{{ $employee->user->getImageUrl() }}" alt="image">
                </div>
            </div>
        </div>

    </div>
</div>

@endforeach

