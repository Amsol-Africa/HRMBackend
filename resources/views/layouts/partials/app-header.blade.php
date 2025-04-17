<div class="app__header__area">
    <div class="app__header-inner">
        <div class="app__header-left">
            <div class="">
                <a id="sidebar__active" class="app__header-toggle" href="javascript:void(0)">
                    <div class="bar-icon-2">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center me-3">
                    <img src="{{ $currentBusiness->getImageUrl() }}" style="height: 20px" alt="">
                    <h2 class="header__title ms-1">{{ $currentBusiness->company_name }}. </h2>
                </div>

                @if (auth()->user()->hasRole('business-admin'))

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Switch Business
                    </a>
                    <ul class="dropdown-menu">
                        @if ($managedBusinesses->isNotEmpty())
                        @foreach ($managedBusinesses as $managed_business)
                        <li>
                            <a class="dropdown-item" onclick="event.preventDefault(); bsImpersonate(this)"
                                data-business="{{ $managed_business->slug }}" href="#">
                                {{ $managed_business->company_name }} </a>
                        </li>
                        @endforeach
                        @else
                        <li>
                            <a class="dropdown-item" onclick="event.preventDefault()" href="#"> No managed businesses
                                found </a>
                        </li>
                        @endif
                    </ul>
                </li>

                @endif

            </div>
        </div>
        <div class="app__header-right">
            <div class="app__herader-input p-relative">
                <input type="search" id="search-field" name="search-field" placeholder="Search Here . . .">
                <button><i class="icon-magnifying-glass"></i></button>
            </div>
            <div class="app__header-action">
                <ul>
                    <li>
                        <div class="nav-item p-relative">
                            <div class="email__dropdown">
                                <div class="notification__card card__scroll">
                                    <div class="notification__header">
                                        <div class="notification__inner">
                                            <h5>Email Notifications</h5>
                                            <span>(8)</span>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar1.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <p><a href="email-read.html">HRM: New policy updates available.</a></p>
                                            <div class="notification__time">
                                                <span>1h ago</span>
                                                <span class="status">HRM</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar2.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <p><a href="email-read.html">CRM: Monthly performance report.</a></p>
                                            <div class="notification__time">
                                                <span>2h ago</span>
                                                <span class="status">CRM</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar3.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <p><a href="email-read.html">HRM: Team meeting at 3 PM.</a></p>
                                            <div class="notification__time">
                                                <span>3h ago</span>
                                                <span class="status">HRM</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="nav-item p-relative">
                            <a id="notifydropdown" href="#">
                                <div class="notification__icon">
                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_209_757)">
                                            <path
                                                d="M9.1665 22C7.27185 22 5.729 20.4582 5.729 18.5625C5.729 18.183 6.037 17.875 6.4165 17.875C6.79601 17.875 7.104 18.183 7.104 18.5625C7.104 19.7002 8.02985 20.625 9.1665 20.625C10.3032 20.625 11.229 19.7002 11.229 18.5625C11.229 18.183 11.537 17.875 11.9165 17.875C12.296 17.875 12.604 18.183 12.604 18.5625C12.604 20.4582 11.0613 22 9.1665 22Z"
                                                fill="#7A7A7A" />
                                            <path
                                                d="M16.7291 19.2499H1.60411C0.719559 19.2499 0 18.5304 0 17.6458C0 17.1764 0.204437 16.7319 0.560944 16.4266C0.583939 16.4065 0.608612 16.3882 0.634293 16.3715C1.97992 15.1973 2.75 13.5079 2.75 11.724V9.16655C2.75 6.18106 4.77306 3.61805 7.66975 2.93323C8.04002 2.84797 8.41046 3.07439 8.49757 3.44483C8.58452 3.81426 8.35541 4.18453 7.98698 4.27164C5.71266 4.80875 4.125 6.82174 4.125 9.16655V11.724C4.125 13.9388 3.15417 16.0343 1.46396 17.4724C1.4502 17.4835 1.43828 17.4936 1.42351 17.5037C1.39883 17.5349 1.375 17.5826 1.375 17.6458C1.375 17.7704 1.47957 17.8749 1.60411 17.8749H16.7291C16.8538 17.8749 16.9584 17.7704 16.9584 17.6458C16.9584 17.5815 16.9346 17.5349 16.9089 17.5037C16.8951 17.4936 16.8822 17.4835 16.8694 17.4724C16.0482 16.7722 15.3999 15.9271 14.9436 14.9599C14.7804 14.617 14.9269 14.2073 15.2707 14.0442C15.6173 13.881 16.0233 14.0296 16.1856 14.3723C16.5485 15.1387 17.0573 15.8116 17.7008 16.3744C17.7246 16.3908 17.7495 16.4083 17.7704 16.4266C18.129 16.7319 18.3334 17.1764 18.3334 17.6458C18.3334 18.5304 17.6138 19.2499 16.7291 19.2499Z"
                                                fill="#7A7A7A" />
                                            <path
                                                d="M16.0417 11.9166C12.7565 11.9166 10.0835 9.24365 10.0835 5.95839C10.0835 2.67296 12.7565 0 16.0417 0C19.3271 0 22.0001 2.67296 22.0001 5.95839C22.0001 9.24365 19.3271 11.9166 16.0417 11.9166ZM16.0417 1.375C13.5145 1.375 11.4585 3.43112 11.4585 5.95839C11.4585 8.48566 13.5145 10.5416 16.0417 10.5416C18.569 10.5416 20.6251 8.48566 20.6251 5.95839C20.6251 3.43112 18.569 1.375 16.0417 1.375Z"
                                                fill="#7A7A7A" />
                                            <path
                                                d="M16.2709 8.70828C15.8914 8.70828 15.5834 8.40028 15.5834 8.02078V5.0415H15.125C14.7455 5.0415 14.4375 4.73351 14.4375 4.354C14.4375 3.9745 14.7455 3.6665 15.125 3.6665H16.2709C16.6504 3.6665 16.9584 3.9745 16.9584 4.354V8.02078C16.9584 8.40028 16.6504 8.70828 16.2709 8.70828Z"
                                                fill="#7A7A7A" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_209_757">
                                                <rect width="22" height="22" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                            </a>
                            <div class="notification__dropdown item-two">
                                <div class="notification__card card__scroll">
                                    <div class="notification__header">
                                        <div class="notification__inner">
                                            <h5>Notifications</h5>
                                            <span>(8)</span>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar1.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <a href="project-details.html">HRM: New employee onboarding session at 10
                                                AM.</a>
                                            <div class="notification__time">
                                                <span>1h ago</span>
                                                <span class="status">HRM</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar7.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <a href="project-details.html">HRM: Training session on compliance starts at
                                                3 PM.</a>
                                            <div class="notification__time">
                                                <span>7h ago</span>
                                                <span class="status">HRM</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification__item">
                                        <div class="notification__thumb">
                                            <a href="employee-profile.html"><img src="/assets/images/avatar/avatar8.png"
                                                    alt="image not found"></a>
                                        </div>
                                        <div class="notification__content">
                                            <a href="project-details.html">CRM: Client satisfaction survey results
                                                available.</a>
                                            <div class="notification__time">
                                                <span>8h ago</span>
                                                <span class="status">CRM</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="nav-item p-relative">
                <a id="userportfolio" href="#">
                    <div class="user__portfolio">
                        <div class="user__portfolio-thumb">
                            @php
                            $employee = auth()->user()->employee;
                            $imageUrl = $employee?->getFirstMediaUrl('avatars');
                            @endphp

                            @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="User {{ auth()->user()->name }}"
                                class="rounded-circle border object-fit-cover" style="width: 80px; height: 80px;">
                            @else
                            <div class="user__initials d-flex align-items-center justify-content-center rounded-circle border bg-secondary text-white"
                                style="width: 80px; height: 80px; font-size: 32px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            @endif
                        </div>

                        <div class="user__content">
                            <h5>{{ auth()->user()->name }}</h5>
                            <span class="status-dot online">online</span>
                        </div>
                    </div>
                </a>
                <div class="user__dropdown">
                    <ul>
                        <li>
                            <a href=""> <i class="fa-solid fa-user-circle"></i> Profile</a>
                        </li>
                        <li>
                            <a href=""> <i class="fa-solid fa-message"></i> messages</a>
                        </li>
                        <li>
                            <a href=""> <i class="fa-solid fa-folder-open"></i> documents</a>
                        </li>
                        <li>
                            <a href="" onclick="event.preventDefault(); logout(this)"> <i
                                    class="fa-solid fa-sign-out-alt"></i> Log Out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="body__overlay"></div>