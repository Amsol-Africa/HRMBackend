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



        <div class="col-xxl-4 col-xl-6 col-lg-12">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card__title-wrap d-flex align-items-center justify-content-between mb-20">
                    <h5 class="card__heading-title">Employee Activities</h5>
                    <div class="card__dropdown">
                        <div class="dropdown">
                            <button>
                                <i class="fa-regular fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-list">
                                <a class="dropdown__item" href="javascript:void(0)">Action</a>
                                <a class="dropdown__item" href="javascript:void(0)">More Action</a>
                                <a class="dropdown__item" href="javascript:void(0)">Another Action</a>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="timeline">
                    <li class="timeline__item d-flex gap-10">
                        <div class="timeline__icon"><span><i class="fa-light fa-box"></i></span></div>
                        <div class="timeline__content w-100">
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-between">
                                <h5 class="small">Purchased from MediaTek</h5>
                                <span class="bd-badge bg-success">04 Mins Ago</span>
                            </div>
                            <p>Lorem ipsum dolor sit amet consecte</p>
                            <div class="timeline__thumb">
                                <img src="assets/images/product/item1.png" alt="image">
                                <img src="assets/images/product/item2.png" alt="image">
                                <img src="assets/images/product/item3.png" alt="image">
                            </div>
                        </div>
                    </li>
                    <li class="timeline__item d-flex gap-10">
                        <div class="timeline__icon"><span><i class="fa-light fa-box"></i></span></div>
                        <div class="timeline__content w-100">
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-between">
                                <h5 class="small">Purchased from MediaTek</h5>
                                <span class="bd-badge bg-success">04 Mins Ago</span>
                            </div>
                            <p>Lorem ipsum dolor sit amet consecte</p>
                            <div class="avatar">
                                <ul>
                                    <li><img class="img-48 border-circle" src="assets/images/avatar/avatar1.png" alt="image"></li>
                                    <li><img class="img-48 border-circle" src="assets/images/avatar/avatar2.png" alt="image"></li>
                                    <li><img class="img-48 border-circle" src="assets/images/avatar/avatar3.png" alt="image"></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="timeline__item d-flex gap-10">
                        <div class="timeline__icon"><span><i class="fa-light fa-box"></i></span></div>
                        <div class="timeline__content w-100">
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-between">
                                <h5 class="small">Purchased from MediaTek</h5>
                                <span class="bd-badge bg-success">04 Mins Ago</span>
                            </div>
                            <p><span class="text-danger text-decoration-underline">3 days left</span> notification to
                                submit
                                new products</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-xxl-8 col-xl-6 col-lg-12">
            <div class="card__wrapper height-equal" style="min-height: 459px;">
                <div class="card-header border-0">
                    <h5 class="card__heading-title"> <i class="fa-solid fa-bell"></i> You have tasks to be done..!</h5>
                </div>

            </div>
        </div>


    </div>
</x-app-layout>
