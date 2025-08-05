<x-app-layout>

    @php
        $statusColors = [
            'pending' => ['icon' => 'fa fa-clock', 'color' => '#ffc107'], // Yellow
            'approved' => ['icon' => 'fa fa-check-circle', 'color' => '#28a745'], // Green
            'declined' => ['icon' => 'fa fa-times-circle', 'color' => '#dc3545'], // Red
            'active' => ['icon' => 'fa fa-play-circle', 'color' => '#007bff'], // Blue
            'used_up' => ['icon' => 'fa fa-calendar-check', 'color' => '#6c757d'], // Gray
        ];
    @endphp

    <div class="row">
        @php
            $col = "col-md-8";
        @endphp

        @if (!empty($timelineData) && is_null($timelineData[0]->approved_by) && auth()->user()->hasRole('business-admin'))
            @php
                $col = "col-md-12";
            @endphp

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <button type="button" onclick="manageLeave(this)" data-action="approve"
                            data-leave="{{ $timelineData[0]->reference_number }}"
                            class="btn btn-success">
                            <i class="fa-solid fa-check"></i> Approve Leave
                        </button>

                        <button type="button" onclick="manageLeave(this)" data-action="reject"
                            data-leave="{{ $timelineData[0]->reference_number }}"
                            class="btn btn-danger">
                            <i class="fa-solid fa-ban"></i> Deny Leave Request
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-8">
            <div class="card__wrapper">
                <div class="card__title-wrap mb-20">
                    <h5 class="card__heading-title">{{ $page }}</h5>
                </div>
                <div class="bd-timeline-wrapper-2">
                    @foreach ($timelineData as $leaveRequest)
                        @foreach ($leaveRequest->statuses as $status)
                            <div class="bd-timeline-item-2">
                                <a href="#" class="bd-timeline-content-2">
                                    <div class="bd-timeline-icon-2">
                                        <i class="{{ $statusColors[$status['name']]['icon'] }}"></i>
                                    </div>
                                    <div class="bd-timeline-step"><span>{{ ucfirst($status['name']) }}</span></div>
                                    <h5 class="title">{{ $leaveRequest->employee_name }} - {{ $leaveRequest->leave_type }}</h5>
                                    <p class="description">
                                        <span class="h4">Status: {{ $status['name'] }}</span><br>
                                        Date: {{ $status['created_at'] }}<br>
                                        @if ($status['reason'])
                                            Reason: {{ $status['reason'] }}
                                        @endif
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/main/leave.js') }}" type="module"></script>
        <script>
            document.getElementById('status').addEventListener('change', function() {
                if (this.value === 'rejected') {
                    document.getElementById('rejectionReason').style.display = 'block';
                } else {
                    document.getElementById('rejectionReason').style.display = 'none';
                }
            });

            $(document).ready(() => {
                getLeave('pending');
                $('#myTab button').on('click', function (event) {
                    event.preventDefault();
                    $(this).tab('show');
                    const status = $(this).attr('aria-controls');
                    getLeave(1, status)
                });
            });
        </script>
    @endpush

</x-app-layout>
