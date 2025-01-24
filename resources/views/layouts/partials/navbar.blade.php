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
                    <a href="{{ route('business.index', $currentBusiness->slug) }}" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-house-door"></i></div>
                        <span class="sidebar__menu-label">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar__menu-category"><span class="category-name">Organization</span></li>

                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-people"></i></div>
                        <span class="sidebar__menu-label">Organization Setup</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.create', $currentBusiness->slug) }}">Configure Organization</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.clients.index', $currentBusiness->slug) }}">Clients</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Holidays</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Work Shifts</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="payroll.html">Pay Grades</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="payroll.html">Education Levels</a>
                        </li>
                    </ul>
                </li>

                <!-- Employee Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-people"></i></div>
                        <span class="sidebar__menu-label">Employee Management</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.create', $currentBusiness->slug) }}">Add Employee</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.index', $currentBusiness->slug) }}">List Employees</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.import', $currentBusiness->slug) }}">Import Employees</a>
                        </li>
                    </ul>
                </li>

                <!-- Payroll Settings Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item active">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-receipt"></i></div>
                        <span class="sidebar__menu-label">Payroll Settings</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.payroll.formula.create', $currentBusiness->slug) }}">Add Formulas</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.payroll.formula', $currentBusiness->slug) }}">List Formulas</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.relief.index', $currentBusiness->slug) }}">Relief</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item active" href="{{ route('business.deductions.index', $currentBusiness->slug) }}">Deductions</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Pay Grades</a>
                        </li>
                    </ul>
                </li>

                <!-- Payroll Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-receipt"></i></div>
                        <span class="sidebar__menu-label">Payroll Management</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.create', $currentBusiness->slug) }}">Process Payroll</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.employees.index', $currentBusiness->slug) }}">Payslips</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Advances</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Loans</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Close Month</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Closed Months</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="recruitment.html">Import Payrolls</a>
                        </li>
                    </ul>
                </li>

                <!-- Leave Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-receipt"></i></div>
                        <span class="sidebar__menu-label">Leave Management</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.index', $currentBusiness->slug) }}">Leave Requests</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.types', $currentBusiness->slug) }}">Leave Types</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.periods', $currentBusiness->slug) }}">Leave Periods</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.entitlement', $currentBusiness->slug) }}">Leave Entitlement</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.settings', $currentBusiness->slug) }}">Settings</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="{{ route('business.leave.reports', $currentBusiness->slug) }}">Leave Reports</a>
                        </li>
                    </ul>
                </li>

                <!-- Performance Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-bar-chart-line"></i></div>
                        <span class="sidebar__menu-label">Performance</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="performance-reports.html">Review Periods</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="feedback.html">KPI Categories</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="feedback.html">KPIs</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="feedback.html">Appraisals</a>
                        </li>
                    </ul>
                </li>

                <!-- Asset Management Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-box"></i></div>
                        <span class="sidebar__menu-label">Recruitments</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="assets.html">Vacancies</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="asset-allocation.html">Candidates</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="asset-allocation.html">Interview Templates</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="asset-allocation.html">Email Templates</a>
                        </li>
                    </ul>
                </li>

                <!-- Time & Attendance Dropdown -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="sidebar__menu-item">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="bi bi-clock"></i></div>
                        <span class="sidebar__menu-label">Time & Attendance</span>
                    </a>
                    <ul class="sidebar-menu child1">
                        <li class="slide">
                            <a class="sidebar__menu-item" href="time-attendance.html">Track Attendance</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Overtime</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Overtime Rates</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Absenteeism</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Clock In Out</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="leave-management.html">Attendance Reports</a>
                        </li>
                    </ul>
                </li>

                <!-- Settings Dropdown -->
                <li class="sidebar__menu-category"><span class="category-name">Settings</span></li>

                <li class="slide">
                    <a href="{{ route('business.job-categories.index', $currentBusiness->slug) }}" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-building-gear"></i></div>
                        <span class="sidebar__menu-label">Job Categories</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('business.departments.index', $currentBusiness->slug) }}" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-building-gear"></i></div>
                        <span class="sidebar__menu-label">Departments</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('business.shifts.index', $currentBusiness->slug) }}" class="sidebar__menu-item">
                        <div class="side-menu__icon"><i class="bi bi-building-gear"></i></div>
                        <span class="sidebar__menu-label">Shifts</span>
                    </a>
                </li>

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
