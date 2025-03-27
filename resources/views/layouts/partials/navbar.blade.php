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

                @include('layouts.partials.switch-role')

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
                        <li class="slide {{ request()->routeIs('business.pay-grades.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.pay-grades.index') ? 'active' : '' }}"
                                href="{{ route('business.pay-grades.index', $currentBusiness->slug) }}">
                                Pay Grades
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Employee Management Dropdown -->
                <li
                    class="slide has-sub {{ request()->routeIs('employees.*') || request()->routeIs('business.*.employees.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item d-flex align-items-center justify-content-between {{ request()->routeIs('employees.*') || request()->routeIs('business.*.employees.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#employeeManagementMenu"
                        aria-expanded="{{ request()->routeIs('employees.*') || request()->routeIs('business.*.employees.*') ? 'true' : 'false' }}"
                        aria-controls="employeeManagementMenu">
                        <div class="d-flex align-items-center">
                            <div class="side-menu__icon me-2"><i class="fa-solid fa-users text-primary"></i></div>
                            <span class="sidebar__menu-label fw-medium">Employee Management</span>
                        </div>
                        <i class="fa-solid fa-angle-down side-menu__angle text-muted"></i>
                    </a>
                    <ul id="employeeManagementMenu"
                        class="sidebar-menu child1 collapse {{ request()->routeIs('employees.*') || request()->routeIs('business.*.employees.*') ? 'show' : '' }}">
                        <li class="slide {{ request()->routeIs('business.employees.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item d-flex align-items-center {{ request()->routeIs('business.employees.index') ? 'active' : '' }}"
                                href="{{ route('business.employees.index', $currentBusiness->slug) }}">
                                List Employees
                            </a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item d-flex align-items-center" href="#"
                                onclick="createEmployee(); return false;">
                                Add Employee
                            </a>
                        </li>
                        <!-- Placeholder for Import Employees (route not defined yet) -->
                        <li class="slide {{ request()->routeIs('business.*.employees.import') ? 'active' : '' }}">
                            <a class="sidebar__menu-item d-flex align-items-center {{ request()->routeIs('business.*.employees.import') ? 'active' : '' }}"
                                href="{{ route('business.employees.import', $currentBusiness->slug) }}">
                                Import Employees
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.employees.warning') ? 'active' : '' }}">
                            <a class="sidebar__menu-item d-flex align-items-center {{ request()->routeIs('business.employees.warning') ? 'active' : '' }}"
                                href="{{ route('business.employees.warning', $currentBusiness->slug) }}">
                                Employee Warnings
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payroll Management Dropdown -->
                <li
                    class="slide has-sub {{ request()->routeIs('business.payroll.index') || request()->routeIs('business.advances.index') || request()->routeIs('business.loans.index') || request()->routeIs('business.employee-reliefs.index') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.payroll.index') || request()->routeIs('business.advances.index') || request()->routeIs('business.loans.index') || request()->routeIs('business.employee-reliefs.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-folder-open"></i></div>
                        <span class="sidebar__menu-label">Payrolls</span>
                    </a>

                    <ul
                        class="sidebar-menu child1 {{ request()->routeIs('business.payroll.index') || request()->routeIs('business.advances.index') || request()->routeIs('business.loans.index') || request()->routeIs('business.employee-reliefs.index') ? 'active' : '' }}">
                        <li class="slide">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.index') ? 'active' : '' }}"
                                href="{{ route('business.payroll.index', $currentBusiness->slug) }}">
                                Run Payroll
                            </a>
                        </li>
                        <li class="slide">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll.all') ? 'active' : '' }}"
                                href="{{ route('business.payroll.all', $currentBusiness->slug) }}">
                                All Payrolls
                            </a>
                        </li>
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
                            <a class="sidebar__menu-item {{ request()->routeIs('business.employee-reliefs.index') ? 'active' : '' }}"
                                href="{{ route('business.employee-reliefs.index', $currentBusiness->slug) }}">
                                Reliefs
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Payroll Settings Management Dropdown -->
                <li
                    class="slide has-sub {{ request()->routeIs('business.payroll-formulas.index') || request()->routeIs('business.reliefs.*') || request()->routeIs('business.payroll.deductions') || request()->routeIs('business.allowances.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.payroll-formulas.index') || request()->routeIs('business.reliefs.*') || request()->routeIs('business.payroll.deductions') || request()->routeIs('business.allowances.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-sack-dollar"></i></div>
                        <span class="sidebar__menu-label">Payroll Settings</span>
                    </a>
                    <ul
                        class="sidebar-menu child1 {{ request()->routeIs('business.payroll-formulas.index') || request()->routeIs('business.reliefs.*') || request()->routeIs('business.payroll.deductions') || request()->routeIs('business.allowances.*') ? 'active' : '' }}">
                        <li class="slide {{ request()->routeIs('business.payroll-formulas.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.payroll-formulas.index') ? 'active' : '' }}"
                                href="{{ route('business.payroll-formulas.index', $currentBusiness->slug) }}">
                                Statutory Deductions
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.reliefs.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.reliefs.index') ? 'active' : '' }}"
                                href="{{ route('business.reliefs.index', $currentBusiness->slug) }}">
                                Manage Reliefs
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.deductions') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.deductions') ? 'active' : '' }}"
                                href="{{ route('business.deductions', $currentBusiness->slug) }}">
                                Manage Other Deductions
                            </a>
                        </li>
                        <li class="slide {{ request()->routeIs('business.allowances.index') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.allowances.index') ? 'active' : '' }}"
                                href="{{ route('business.allowances.index', $currentBusiness->slug) }}">
                                Manage Allowances
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
                <!-- Time & Attendance Dropdown -->
                <li
                    class="slide has-sub {{ request()->routeIs('business.attendances.*', 'business.overtime.*', 'business.absenteeism.*', 'business.clock-in-out.*', 'business.reports.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"
                        class="sidebar__menu-item {{ request()->routeIs('business.attendances.*', 'business.overtime.*', 'business.absenteeism.*', 'business.clock-in-out.*', 'business.reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-angle-down side-menu__angle"></i>
                        <div class="side-menu__icon"><i class="fa-solid fa-clock"></i></div>
                        <span class="sidebar__menu-label">Time & Attendance</span>
                    </a>
                    <ul
                        class="sidebar-menu child1 {{ request()->routeIs('business.attendances.*', 'business.overtime.*', 'business.absenteeism.*', 'business.clock-in-out.*', 'business.reports.*') ? 'active' : '' }}">
                        <li class="slide {{ request()->routeIs('business.attendances.clock-in') ? 'active' : '' }}">
                            <a class="sidebar__menu-item {{ request()->routeIs('business.attendances.clock-in') ? 'active' : '' }}"
                                href="{{ route('business.attendances.clock-in', $currentBusiness) }}">
                                Clock In / Out
                            </a>
                        </li>
                        {{-- <li class="slide {{ request()->routeIs('business.attendances.clock-out') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.attendances.clock-out') ? 'active' : '' }}"
                            href="{{ route('business.attendances.clock-out', $currentBusiness) }}">
                            Clock Out
                        </a>
                </li> --}}
                <li class="slide {{ request()->routeIs('business.attendances.index') ? 'active' : '' }}">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.attendances.index') ? 'active' : '' }}"
                        href="{{ route('business.attendances.index', $currentBusiness) }}">
                        Attendances
                    </a>
                </li>
                <li class="slide {{ request()->routeIs('business.overtime.index') ? 'active' : '' }}">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.overtime.index') ? 'active' : '' }}"
                        href="{{ route('business.overtime.index', $currentBusiness) }}">
                        Overtime
                    </a>
                </li>
                <li class="slide {{ request()->routeIs('business.attendances.monthly') ? 'active' : '' }}">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.attendances.monthly') ? 'active' : '' }}"
                        href="{{ route('business.attendances.monthly', $currentBusiness) }}">
                        Monthly Attendance
                    </a>
                </li>
                <li class="slide {{ request()->routeIs('business.reports.index') ? 'active' : '' }}">
                    <a class="sidebar__menu-item {{ request()->routeIs('business.reports.index') ? 'active' : '' }}"
                        href="{{ route('business.reports.index', $currentBusiness) }}">
                        Attendance Reports
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
                    <li class="slide {{ request()->routeIs('business.performance.kpis') ? 'active' : '' }}">
                        <a class="sidebar__menu-item {{ request()->routeIs('business.performance.kpis') ? 'active' : '' }}"
                            href="{{ route('business.performance.kpis.index', $currentBusiness->slug) }}">
                            KPIs
                        </a>
                    </li>
                    <li class="slide {{ request()->routeIs('business.performance.kpis.create') ? 'active' : '' }}">
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

            <!-- Settings Dropdown -->
            <li class="sidebar__menu-category"><span class="category-name">Account</span></li>

            <li class="slide {{ request()->routeIs('business.profile.index') ? 'active' : '' }}">
                <a href="{{ route('business.profile.index', $currentBusiness->slug) }}"
                    class="sidebar__menu-item {{ request()->routeIs('business.profile.index') ? 'active' : '' }}">
                    <div class="side-menu__icon"><i class="fa-solid fa-user-cog"></i></div>
                    <span class="sidebar__menu-label">My Account</span>
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