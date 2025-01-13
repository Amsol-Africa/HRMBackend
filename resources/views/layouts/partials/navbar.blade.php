<div class="app-sidebar" id="sidebar">
    <div class="main-sidebar-header">
        <a href="index.html" class="header-logo">
            <img class="main-logo" src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
            <img class="dark-logo" src="{{ asset('media/amsol-logo.png') }}" alt="{{ config('app.name') }}">
        </a>
    </div>
    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="sidebar-left" id="sidebar-left"></div>
            <ul class="main-menu" style="padding-top: 70px">
                <li class="sidebar__menu-category"><span class="category-name">Main</span></li>

                <!-- Dashboard -->
                <li class="slide">
                    <a href="dashboard.html" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-house-door"></i></div>
                        <span class="sidebar__menu-label">Dashboard</span>
                    </a>
                </li>

                <!-- Employee Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-regular fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-people"></i></div>
                        <span class="sidebar__menu-label">Employee Management</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="employees.html">View Employees</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Recruitment</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="payroll.html">Payroll</a>
                        </li>
                    </ul>
                </li>

                <!-- Performance Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-regular fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-bar-chart-line"></i></div>
                        <span class="sidebar__menu-label">Performance</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="performance-reports.html">View Reports</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="feedback.html">Employee Feedback</a>
                        </li>
                    </ul>
                </li>

                <!-- Asset Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-regular fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-box"></i></div>
                        <span class="sidebar__menu-label">Asset Management</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="assets.html">View Assets</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="asset-allocation.html">Asset Allocation</a>
                        </li>
                    </ul>
                </li>

                <!-- Time & Attendance Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-regular fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-clock"></i></div>
                        <span class="sidebar__menu-label">Time & Attendance</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="time-attendance.html">Track Attendance</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Leave Management</a>
                        </li>
                    </ul>
                </li>

                <!-- Settings Dropdown -->
                <li class="sidebar__menu-category"><span class="category-name">Settings</span></li>

                <li class="slide">
                    <a href="account-settings.html" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-gear"></i></div>
                        <span class="sidebar__menu-label">Account Settings</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="notifications.html" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-bell"></i></div>
                        <span class="sidebar__menu-label">Notifications</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="help.html" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-question-circle"></i></div>
                        <span class="sidebar__menu-label">Help & Support</span>
                    </a>
                </li>

            </ul>
            <div class="sidebar-right" id="sidebar-right"></div>
        </nav>
    </div>
</div>


<div class="app__offcanvas-overlay"></div>
