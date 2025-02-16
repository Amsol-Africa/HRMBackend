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
                <li class="slide {{ request()->routeIs('business.index') ? 'active' : '' }}">
                    <a href="{{ route('business.index', $currentBusiness->slug) }}"
                        class="sidebar__menu-item {{ request()->routeIs('business.index') ? 'active' : '' }}">
                        <div class="side-menu__icon"><i class="fa-solid fa-home"></i></div>
                        <span class="sidebar__menu-label">Dashboard</span>
                    </a>
                </li>

                <!-- Account sharing -->
                <li class="slide {{ request()->routeIs('business.clients.*') ? 'active' : '' }}">
                    <a href="{{ route('business.clients.index', $currentBusiness->slug) }}"
                        class="sidebar__menu-item {{ request()->routeIs('business.clients.*') ? 'active' : '' }}">
                        <div class="side-menu__icon"><i class="fa-solid fa-sign-in-alt"></i></div>
                        <span class="sidebar__menu-label">Clients / Account Sharing</span>
                    </a>
                </li>

                <!-- Business Locations -->
                <li class="slide {{ request()->routeIs('business.locations.*') ? 'active' : '' }}">
                    <a href="{{ route('business.locations.index', $currentBusiness->slug) }}"
                        class="sidebar__menu-item {{ request()->routeIs('business.locations.*') ? 'active' : '' }}">
                        <div class="side-menu__icon"><i class="fa-solid fa-location-dot"></i></div> <span
                            class="sidebar__menu-label">Locations</span>
                    </a>
                </li>

                <li class="sidebar__menu-category"><span class="category-name">Organization</span></li>

                <li
                    class="slide has-sub {{ request()->routeIs('business.organization-setup', 'business.job-categories.index', 'business.departments.index', 'business.shifts.index') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.employees.create', 'business.job-categories.index', 'business.departments.index', 'business.shifts.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-tools"></i></div>
                        <span class="sidebar__menu-label">Organization Setup</span>
                    </a>
                    <ul
                        class="sidebar-menu child1 {{ request()->routeIs('business.organization-setup', 'business.job-categories.index', 'business.departments.index', 'business.shifts.index') ? 'active' : '' }}">
                        <li class="slide {{ request()->routeIs('business.organization-setup') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.organization-setup') ? 'active' : '' }}"
                                href="{{ route('business.organization-setup', $currentBusiness->slug) }}">
                                Configure Organization
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.pay-schedule') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.pay-schedule') ? 'active' : '' }}"
                                href="{{ route('business.pay-schedule', $currentBusiness->slug) }}">
                                Pay Schedule
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.job-categories.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.job-categories.index') ? 'active' : '' }}"
                                href="{{ route('business.job-categories.index', $currentBusiness->slug) }}">
                                Job Categories
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.departments.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.departments.index') ? 'active' : '' }}"
                                href="{{ route('business.departments.index', $currentBusiness->slug) }}">
                                Departments
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.shifts.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.shifts.index') ? 'active' : '' }}"
                                href="{{ route('business.shifts.index', $currentBusiness->slug) }}">
                                Work Shifts
                            </a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="">Pay Grades</a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item" href="">Education Levels</a>
                        </li>
                    </ul>
                </li>

                <!-- Employee Management Dropdown -->
                <li class="slide has-sub {{ request()->routeIs('business.employees.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.employees.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-users"></i></div>
                        <span class="sidebar__menu-label">Employee Management</span>
                    </a>
                    <ul class="sidebar-menu child1 {{ request()->routeIs('business.employees.*') ? 'active' : '' }}">
                        <li class="slide {{ request()->routeIs('business.employees.create') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.employees.create') ? 'active' : '' }}"
                                href="{{ route('business.employees.create', $currentBusiness->slug) }}">
                                Add Employee
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.employees.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.employees.index') ? 'active' : '' }}"
                                href="{{ route('business.employees.index', $currentBusiness->slug) }}">
                                List Employees
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.employees.import') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.employees.import') ? 'active' : '' }}"
                                href="{{ route('business.employees.import', $currentBusiness->slug) }}">
                                Import Employees
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payroll Management Dropdown -->
                <li
                    class="slide has-sub {{ request()->routeIs('business.payroll.process') || request()->routeIs('business.payroll.import') || request()->routeIs('business.advances.index') || request()->routeIs('business.loans.index') || request()->routeIs('business.payroll.index') || request()->routeIs('business.payroll.payslips') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.payroll.process') || request()->routeIs('business.payroll.import') || request()->routeIs('business.loans.index') || request()->routeIs('business.advances.index') || request()->routeIs('business.payroll.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-folder-open"></i></div>
                        <span class="sidebar__menu-label">Payrolls</span>
                    </a>

                    <ul class="sidebar-menu child1">
                        <li class="slide {{ request()->routeIs('business.payroll.process') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.process') ? 'active' : '' }}"
                                href="{{ route('business.payroll.process', $currentBusiness->slug) }}">
                                Process Payroll
                            </a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.index') || request()->routeIs('business.payroll.payslips') ? 'active' : '' }}"
                                href="{{ route('business.payroll.index', $currentBusiness->slug) }}">
                                Past Payrolls
                            </a>
                        </li>
                        {{-- <li class="slide">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.payslips') ? 'active' : '' }}"
                        href="{{ route('business.payroll.payslips', $currentBusiness->slug) }}">
                        Payslips
                        </a>
                </li> --}}
                <li class="slide">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.advances.index') ? 'active' : '' }}"
                        href="{{ route('business.advances.index', $currentBusiness->slug) }}">
                        Salary Advances
                    </a>
                </li>
                <li class="slide">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.loans.index') ? 'active' : '' }}"
                        href="{{ route('business.loans.index', $currentBusiness->slug) }}">
                        Loans
                    </a>
                </li>
                <li class="slide">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.import') ? 'active' : '' }}"
                        href="{{ route('business.payroll.import', $currentBusiness->slug) }}">
                        Import Payrolls
                    </a>
                </li>
            </ul>
            </li>

            <!-- Payroll Settings Management Dropdown -->
            <li
                class="slide has-sub {{ request()->routeIs('business.payroll.formula') || request()->routeIs('business.relief.*') || request()->routeIs('business.deductions.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);"
                    class="sidebar__menu-item {{ request()->routeIs('business.payroll.formula') || request()->routeIs('business.relief.*') || request()->routeIs('business.deductions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-angle-down side-menu__angle"></i>
                    <div class="side-menu__icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <span class="sidebar__menu-label">Payroll Settings</span>
                </a>
                <ul
                    class="sidebar-menu child1 {{ request()->routeIs('business.payroll.formula') || request()->routeIs('business.relief.*') || request()->routeIs('business.deductions.*') ? 'active' : '' }}">
                    <li
                        class="slide {{ request()->routeIs('business.payroll.formula') || request()->routeIs('business.payroll.formula.create') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.formula') ? 'active' : '' }}"
                            href="{{ route('business.payroll.formula', $currentBusiness->slug) }}">
                            Statutory Deductions
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.relief.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.relief.index') ? 'active' : '' }}"
                            href="{{ route('business.relief.index', $currentBusiness->slug) }}">
                            Relief
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.deductions.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.deductions.index') ? 'active' : '' }}"
                            href="{{ route('business.deductions.index', $currentBusiness->slug) }}">
                            Deductions
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.allowances.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.allowances.index') ? 'active' : '' }}"
                            href="{{ route('business.allowances.index', $currentBusiness->slug) }}">
                            Allowances
                        </a>
                    </li>
                    <li class="slide {{ request()->is('recruitment.html') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->is('recruitment.html') ? 'active' : '' }}"
                            href="recruitment.html">
                            Pay Grades
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Leave Management Dropdown -->
            <li class="slide has-sub {{ request()->routeIs('business.leave.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);"
                    class="sidebar__menu-item {{ request()->routeIs('business.leave.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-angle-down side-menu__angle"></i>
                    <div class="side-menu__icon"><i class="fa-solid fa-calendar-check"></i></div>
                    <span class="sidebar__menu-label">Leave Management</span>
                </a>
                <ul class="sidebar-menu child1 {{ request()->routeIs('business.leave.*') ? 'active' : '' }}">
                    <li class="slide {{ request()->routeIs('business.leave.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.leave.index') ? 'active' : '' }}"
                            href="{{ route('business.leave.index', $currentBusiness->slug) }}">
                            Leave Requests
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.leave.types') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.leave.types') ? 'active' : '' }}"
                            href="{{ route('business.leave.types', $currentBusiness->slug) }}">
                            Leave Types
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.leave.periods') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.leave.periods') ? 'active' : '' }}"
                            href="{{ route('business.leave.periods', $currentBusiness->slug) }}">
                            Leave Periods
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.leave.entitlements.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.leave.entitlements.index') ? 'active' : '' }}"
                            href="{{ route('business.leave.entitlements.index', $currentBusiness->slug) }}">
                            Leave Entitlements
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.leave.reports') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.leave.reports') ? 'active' : '' }}"
                            href="{{ route('business.leave.reports', $currentBusiness->slug) }}">
                            Leave Reports
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Performance Management Dropdown -->
            <li class="slide has-sub {{ request()->routeIs('business.performance.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);"
                    class="sidebar__menu-item {{ request()->routeIs('business.performance.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-angle-down side-menu__angle"></i>
                    <div class="side-menu__icon"><i class="fa-solid fa-chart-line"></i></div>
                    <span class="sidebar__menu-label">Performance</span>
                </a>
                <ul class="sidebar-menu child1 {{ request()->routeIs('business.performance.*') ? 'active' : '' }}">
                    <li class="slide {{ request()->routeIs('business.performance.tasks.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.performance.tasks.index') ? 'active' : '' }}"
                            href="{{ route('business.performance.tasks.index', $currentBusiness->slug) }}">
                            Tasks
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.performance.tasks.progress') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.performance.tasks.progress') ? 'active' : '' }}"
                            href="{{ route('business.performance.tasks.progress', $currentBusiness->slug) }}">
                            Task Progress
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.performance.tasks.reports') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.performance.tasks.reports') ? 'active' : '' }}"
                            href="{{ route('business.performance.tasks.reports', $currentBusiness->slug) }}">
                            Task Reports
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.performance.reviews') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.performance.reviews') ? 'active' : '' }}"
                            href="{{ route('business.performance.reviews', $currentBusiness->slug) }}">
                            Performance Reviews
                        </a>
                    </li>
                </ul>
            </li>


            <!-- Asset Management Dropdown -->
            <li
                class="slide has-sub {{ request()->routeIs('business.recruitment.*') || request()->routeIs('business.job-applications.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="sidebar__menu-item">
                    <i class="fa-solid fa-angle-down side-menu__angle"></i>
                    <div class="side-menu__icon"><i class="fa-solid fa-briefcase"></i></div>
                    <span class="sidebar__menu-label">Recruitment</span>
                </a>
                <ul class="sidebar-menu child1">
                    <li
                        class="slide {{ request()->routeIs('business.job-applications.applicants.*') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.job-applications.applicants.*') ? 'active' : '' }}"
                            href="{{ route('business.job-applications.applicants.index', $currentBusiness->slug) }}">
                            Job Applicants
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.recruitment.jobs.*') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.recruitment.jobs.*') ? 'active' : '' }}"
                            href="{{ route('business.recruitment.jobs.index', $currentBusiness->slug) }}">
                            Job Posts
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.job-applications.index') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.job-applications.index') ? 'active' : '' }}"
                            href="{{ route('business.job-applications.index', $currentBusiness->slug) }}">
                            Job Applications
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.recruitment.interviews') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.recruitment.interviews') ? 'active' : '' }}"
                            href="{{ route('business.recruitment.interviews', $currentBusiness->slug) }}">
                            Interview Scheduling
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.recruitment.reports') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.recruitment.reports') ? 'active' : '' }}"
                            href="{{ route('business.recruitment.reports', $currentBusiness->slug) }}">
                            Recruitment Reports
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Time & Attendance Dropdown -->
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="sidebar__menu-item">
                    <i class="fa-solid fa-angle-down side-menu__angle"></i>
                    <div class="side-menu__icon"><i class="fa-solid fa-clock"></i></div>
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
                <a href="account-settings.html" class="sidebar__menu-item">
                    <div class="side-menu__icon"><i class="fa-solid fa-user-cog"></i></div>
                    <span class="sidebar__menu-label">Account Settings</span>
                </a>
            </li>

            <li class="slide">
                <a href="notifications.html" class="sidebar__menu-item">
                    <div class="side-menu__icon"><i class="fa-solid fa-bell"></i></div>
                    <span class="sidebar__menu-label">Notifications</span>
                </a>
            </li>

            <li class="slide">
                <a href="help.html" class="sidebar__menu-item">
                    <div class="side-menu__icon"><i class="fa-solid fa-life-ring"></i></div>
                    <span class="sidebar__menu-label">Help & Support</span>
                </a>
            </li>

            </ul>
            <div class="sidebar-right" id="sidebar-right"></div>
        </nav>
    </div>
</div>


<div class="app__offcanvas-overlay"></div>