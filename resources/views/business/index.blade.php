<x-app-layout title="{{ $page }}">
    <!-- Hidden input for active business slug -->
    <input type="hidden" id="active_business_slug" value="{{ session('active_business_slug') }}">

    <div class="row g-20">
        @foreach ($cards as $card)
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <div class="card__wrapper border">
                <div class="d-flex align-items-center gap-sm">
                    <div class="card__icon">
                        <span><i class="{{ $card['trend_class'] }} {{ $card['icon'] }}"></i></span>
                    </div>
                    <div class="card__title-wrap">
                        <h6 class="card__sub-title mb-10">{{ $card['title'] }}</h6>
                        <div class="d-flex flex-wrap align-items-end gap-10">
                            <h3 class="card__title">{{ $card['value'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-xxl-12 col-xl-6 col-lg-12">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card__heading-title"> <i class="fa-solid fa-bar-chart"></i> Payroll Trends </h5>

                        <form action="payrollTrendsForm">
                            <label for="payrun_year" class="form-label">Year</label>
                            <input type="number" id="payrun_year" name="payrun_year" class="form-control"
                                min="{{ now()->year - 5 }}" max="{{ now()->year + 1 }}" value="{{ now()->year }}">
                        </form>
                    </div>
                </div>

                <div class="card-body mb-0">
                    <div id="payrollChart"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-xl-6 col-lg-12">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-20">
                    <h5 class="card__heading-title">User Activities</h5>
                </div>

                <div class="overflow-auto" style="max-height: 380px;" id="activityLogsContainer">
                    {{ loader() }}
                </div>
            </div>
        </div>

        <div class="col-xxl-8 col-xl-6 col-lg-12">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0" style="margin-bottom: 50px">
                    <h5 class="card__heading-title"> <i class="fa-solid fa-wallet"></i> Process Pay Roll for
                        {{ date('F Y') }}! <span class="badge bg-success">READY</span>
                    </h5>
                </div>

                <div class="row g-2" style="margin-bottom: 50px">
                    <div class="col-md-4">
                        <h5 class="card-title">NO. OF EMPLOYEES</h5> <br>
                        <h5><strong>29</strong></h5>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">PAYMENT DATE</h5> <br>
                        <h5><strong> {{ date('d, F Y') }} </strong></h5>
                    </div>
                    <div class="col-md-4">
                        <h5 class="card-title">TOTAL NET PAY</h5> <br>
                        <a href="" class="btn btn-info">YET TO PROCESS</a>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-12">
                        <a href="" class="btn btn-primary btn-sm w-100"> <i class="fa-solid fa-arrow-right"></i>
                            Run pay roll</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0">
                    <h5 class="card__heading-title"><i class="fa-solid fa-calendar-check"></i> Attendance Trends</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0">
                    <h5 class="card__heading-title"><i class="fa-solid fa-plane-departure"></i> Leave Trends</h5>
                </div>
                <div class="card-body">
                    <div id="leaveChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0">
                    <h5 class="card__heading-title"><i class="fa-solid fa-money-check-alt"></i> Loan Trends</h5>
                </div>
                <div class="card-body">
                    <div id="loanChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('/js/main/businesses.js') }}" type="module"></script>
    <script src="{{ asset('/js/main/trends.js') }}" type="module"></script>
    <script src="{{ asset('/js/main/log-activities.js') }}" type="module"></script>

    <script>
        $(document).ready(() => {
            payrollTrends();
            logActivities();
            loadTrends(new Date().getFullYear());
        });
    </script>
    @endpush
</x-app-layout>