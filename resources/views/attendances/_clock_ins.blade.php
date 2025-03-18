@foreach ($clockins as $clockin)
@php
$employee = $clockin->employee ?? null;
$user = $employee ? $employee->user : null;
@endphp
<div class="card mb-3 p-1">
    <div class="card-body mb-0 p-1">
        <div class="row g-20 align-items-center">
            <div class="col-md-10">
                <div class="card__title-wrap d-flex align-items-center justify-content-between">
                    <h5 class="card__heading-title">{{ $user ? $user->name : 'Unknown Employee' }}</h5>
                </div>

                <p class="text-dark mb-0">
                    <strong>Clock In:</strong>
                    @if($clockin->is_absent)
                    <span class="text-danger">Absent</span>
                    @else
                    {{ $clockin->clock_in ? $clockin->clock_in->format('jS M H:i') : '-' }}
                    @endif
                </p>
                <p class="text-dark">
                    <strong>Clock Out:</strong>
                    {{ $clockin->clock_out ? $clockin->clock_out->format('jS M H:i') : '-' }}
                </p>

                <div class="d-flex align-items-center">
                    <div>
                        @if ($clockin->clock_out)
                        <button type="button" class="btn btn-success" disabled>
                            <i class="bi me-2 bi-check-circle"></i> Clocked Out
                        </button>
                        @else
                        <button type="button" onclick="clockOut(this)" data-employee="{{ $employee->id }}"
                            class="btn btn-danger">
                            <i class="bi me-2 bi-bell"></i> Clock Out
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-2 text-end">
                <div style="height: 120px; width: 120px; overflow: hidden; border-radius: 100%">
                    <img style="height: 100%; width: 100%"
                        src="{{ $user ? $user->getImageUrl() : asset('./avatar/avatar.png') }}" alt="Employee Image">
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach