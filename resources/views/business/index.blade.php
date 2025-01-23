<x-app-layout>

    <div class="row g-20">
        @foreach ($cards as $card)
            <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-6">
                <div class="card__wrapper">
                    <div class="d-flex align-items-center gap-sm">
                        <div class="card__icon">
                            <span><i class="{{ $card['icon'] }}"></i></span>
                        </div>
                        <div class="card__title-wrap">
                            <h6 class="card__sub-title mb-10">{{ $card['title'] }}</h6>
                            <div class="d-flex flex-wrap align-items-end gap-10">
                                <h3 class="card__title">{{ $card['value'] }}</h3>
                                <span class="card__desc style_two">
                                    <span class="{{ $card['trend_class'] }}">
                                        <i class="fa-light {{ $card['trend_icon'] }}"></i> {{ $card['trend_value'] }}
                                    </span>
                                    Than Last {{ $card['time_period'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
