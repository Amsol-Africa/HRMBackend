<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\KPIsController;
use Illuminate\Http\Request;

Route::get('api/jobs/openings', [JobPostController::class, 'fetch'])->name('jobs.openings');

Route::middleware(['auth'])->group(function () {

    Route::post('/switch-role', [RoleSwitchController::class, 'switchRole'])->name('switch.role');

    //setup busines & modules
    Route::name('setup.')->prefix('setup')->group(function () {
        Route::get('business', [BusinessController::class, 'create'])->name('business');
        Route::get('modules', [ModuleController::class, 'create'])->name('modules');
    });

    Route::middleware(['ensure_role', 'role:business-admin'])->name('location.')->prefix('location/{location:slug}')->group(function () {
        Route::get('/payroll/{id}/download-column/{column}/{format}', [PayrollController::class, 'downloadColumn'])->name('payroll.download_column');
    });

    Route::middleware(['ensure_role', 'role:business-admin'])->name('business.')->prefix('business/{business:slug}')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/clients', [DashboardController::class, 'clients'])->name('clients.index');
        Route::get('/locations', [DashboardController::class, 'locations'])->name('locations.index');
        Route::get('/clients/request-access', [DashboardController::class, 'requestAccess'])->name('clients.request-access');
        Route::get('/clients/grant-access', [DashboardController::class, 'grantAccess'])->name('clients.grant-access');
        Route::get('/organization-setup', [DashboardController::class, 'organizationSetup'])->name('organization-setup');
        Route::get('/pay-schedule', [DashboardController::class, 'paySchedule'])->name('pay-schedule');

        Route::get('/departments', [DashboardController::class, 'departments'])->name('departments.index');
        Route::get('/employees', [DashboardController::class, 'employees'])->name('employees.index');
        Route::get('/employees/import', [DashboardController::class, 'importEmployees'])->name('employees.import');
        Route::get('/employees/warning', [DashboardController::class, 'warning'])->name('employees.warning');

        // Added GET routes for downloading templates
        Route::get('/employees/download-csv-template', [EmployeeController::class, 'downloadCsvTemplate'])->name('employees.downloadCsvTemplate');
        Route::get('/employees/download-xlsx-template', [EmployeeController::class, 'downloadXlsxTemplate'])->name('employees.downloadXlsxTemplate');

        Route::get('/job-categories', [DashboardController::class, 'jobCategories'])->name('job-categories.index');
        Route::get('/shifts', [DashboardController::class, 'shifts'])->name('shifts.index');

        Route::get('/payroll-formulas', [DashboardController::class, 'payrollFormulas'])->name('payroll-formulas.index');
        Route::get('/payroll-formulas/bracket-template', function (Request $request) {
            $index = $request->input('index', 0);
            return view('payroll-formulas._bracket', ['index' => $index]);
        })->name('payroll-formulas.bracket-template');


        Route::get('/deductions', [DashboardController::class, 'deductions'])->name('deductions');

        Route::get('/payroll', [DashboardController::class, 'payroll'])->name('payroll.index');
        Route::get('/payroll/all', [DashboardController::class, 'payrollAll'])->name('payroll.all');
        Route::get('/payroll/{id}', [DashboardController::class, 'viewPayroll'])->name('payroll.view');
        Route::get('/payroll/{id}/download/{format}', [DashboardController::class, 'downloadPayroll'])->name('payroll.reports');
        Route::get('/payroll/{id}/download-column/{column}/{format}', [DashboardController::class, 'downloadColumn'])->name('payroll.download_column');
        Route::get('/payroll/{id}/print-all-payslips', [DashboardController::class, 'printAllPayslips'])->name('payroll.print_all_payslips');

        // New route for viewing all payslips for an employee
        Route::get('/payslips', [PayrollController::class, 'viewPayslips'])->name('payslips');
        Route::get('/payroll/payslip/{employee_id}', [PayrollController::class, 'viewPayslip'])->name('payroll.payslip');

        Route::post('/payroll/send-payslips', [PayrollController::class, 'sendPayslips'])->name('payroll.send_payslips');

        Route::get('reliefs', [DashboardController::class, 'reliefs'])->name('reliefs.index');
        Route::get('employee-reliefs', [DashboardController::class, 'employeeReliefs'])->name('employee-reliefs.index');

        Route::get('/allowances', [DashboardController::class, 'allowances'])->name('allowances.index');

        Route::get('/advances', [DashboardController::class, 'advances'])->name('advances.index');
        Route::get('/loans', [DashboardController::class, 'loans'])->name('loans.index');

        //leave management
        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/requests', [DashboardController::class, 'leaveApplications'])->name('index');
            Route::get('/requests/create', [DashboardController::class, 'requestLeave'])->name('create');
            Route::get('/view/{leave}', [DashboardController::class, 'leaveApplication'])->name('show');
            Route::get('/types', [DashboardController::class, 'leaveTypes'])->name('types');
            Route::get('/periods', [DashboardController::class, 'leavePeriods'])->name('periods');
            Route::get('/entitlements', [DashboardController::class, 'leaveEntitlements'])->name('entitlements.index');
            Route::get('/entitlements/set', [DashboardController::class, 'setLeaveEntitlements'])->name('entitlements.create');
            Route::get('/settings', [DashboardController::class, 'leaveSettings'])->name('settings');
            Route::get('/reports', [DashboardController::class, 'leaveReports'])->name('reports');
        });

        // Recruitment Module
        Route::prefix('recruitment')->name('recruitment.')->group(function () {
            Route::get('/applicants', [DashboardController::class, 'applicants'])->name('applicants');
            Route::get('/job-posts', [DashboardController::class, 'jobPosts'])->name('jobs.index');
            Route::get('/job-posts/create', [DashboardController::class, 'createJobPosts'])->name('jobs.create');
            Route::get('/job-posts/{jobpost}/edit', [DashboardController::class, 'editJobPosts'])->name('jobs.edit');
            Route::get('/interviews', [DashboardController::class, 'interviews'])->name('interviews');
            Route::get('/reports', [DashboardController::class, 'recruitmentReports'])->name('reports');
        });

        // Recruitment Module
        Route::prefix('job-applications')->name('job-applications.')->group(function () {
            Route::get('/', [DashboardController::class, 'jobApplications'])->name('index');
            Route::get('/create', [DashboardController::class, 'createJobApplications'])->name('create');
            Route::get('/applicants', [DashboardController::class, 'jobApplicants'])->name('applicants.index');
            Route::get('/applicants/create', [DashboardController::class, 'createJobApplicants'])->name('applicants.create');
        });

        // Performance module
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::prefix('tasks')->name('tasks.')->group(function () {
                Route::get('/', [DashboardController::class, 'tasks'])->name('index');
                Route::get('/create', [DashboardController::class, 'create'])->name('create');
                Route::get('/progress/{task}', [DashboardController::class, 'progress'])->name('progress');
                Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
                Route::get('/{task}', [DashboardController::class, 'show'])->name('show');
            });
            Route::get('/reviews', [DashboardController::class, 'reviews'])->name('reviews');
            Route::prefix('kpis')->name('kpis.')->group(function () {
                Route::get('/', [KpisController::class, 'index'])->name('index');
                Route::get('/create', [KpisController::class, 'create'])->name('create');
                Route::get('/results', [KpisController::class, 'results'])->name('results');
                Route::get('/edit', [KpisController::class, 'edit'])->name('edit');
            });
        });

        // Attendance Module
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [DashboardController::class, 'attendances'])->name('index');
            Route::get('/monthly', [DashboardController::class, 'monthlyAttendances'])->name('monthly');
            Route::get('/clock-in', [DashboardController::class, 'clockIn'])->name('clock-in');
            Route::get('/clock-out', [DashboardController::class, 'clockOut'])->name('clock-out');
        });

        // Attendance Module
        Route::prefix('downloads')->name('downloads.')->group(function () {
            Route::get('/', [DashboardController::class, 'attendances'])->name('index');
        });

        // Overtime Routes
        Route::prefix('overtime')->name('overtime.')->group(function () {
            Route::get('/', [DashboardController::class, 'overtime'])->name('index');
            Route::get('/rates', [DashboardController::class, 'overtimeRates'])->name('rates');
        });

        // Clock In/Out Route
        Route::get('clock-in-out', [DashboardController::class, 'clockInOut'])->name('clock-in-out.index');

        // Attendance Reports Route
        Route::get('reports', [DashboardController::class, 'attendanceReport'])->name('reports.index');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.index');

        Route::get('pay-grades', [DashboardController::class, 'payGrades'])->name('pay-grades.index');
    });

    Route::middleware(['ensure_role', 'role:business-employee'])->name('myaccount.')->prefix('myaccount/{business:slug}')->group(function () {
        Route::get('/', [EmployeeDashboardController::class, 'index'])->name('index');

        // Profile Routes
        Route::get('update-details', [EmployeeDashboardController::class, 'updateDetails'])->name('update');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile');

        // Leave Management
        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/requests', [EmployeeDashboardController::class, 'viewLeaves'])->name('requests.index');
            Route::get('/requests/create', [EmployeeDashboardController::class, 'requestLeave'])->name('requests.create');
            Route::get('/view/{leave}', [EmployeeDashboardController::class, 'leaveApplication'])->name('show');
        });
        // Attendance Module
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [EmployeeDashboardController::class, 'attendances'])->name('index');
            Route::get('clock-in-out', [EmployeeDashboardController::class, 'clockInOut'])->name('clock-in-out.index');
        });

        // Overtime Routes
        Route::prefix('overtime')->name('overtime.')->group(function () {
            Route::get('/', [DashboardController::class, 'overtime'])->name('index');
            Route::get('/rates', [DashboardController::class, 'overtimeRates'])->name('rates');
        });

        // Absenteeism Route
        Route::get('absenteeism', [DashboardController::class, 'absenteeism'])->name('absenteeism.index');

        // Attendance Reports Route
        Route::get('reports', [DashboardController::class, 'attendanceReport'])->name('reports.index');

        // Attendance
        Route::get('/attendance', [EmployeeDashboardController::class, 'checkIn'])->name('attendance');

        // P9 Form (Tax Document)
        Route::get('/p9', [EmployeeDashboardController::class, 'downloadP9'])->name('p9');

        // Payment Slips
        Route::get('/payslips', [EmployeeDashboardController::class, 'viewPayslips'])->name('payslips');
        Route::get('/payslips/download/{id}', [EmployeeDashboardController::class, 'downloadPayslip'])->name('payslips.download');

        Route::middleware('auth')->group(function () {
            Route::get('/account-settings', [EmployeeDashboardController::class, 'accountSettings'])->name('account.settings');
        });

        Route::get('/notifications', function () {
            return view('employee.notifications');
        })->name('notifications');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/payroll-template/csv', [PayrollController::class, 'downloadCsvTemplate'])->name('payroll-template.csv');
    Route::get('/payroll-template/xlsx', [PayrollController::class, 'downloadXlsxTemplate'])->name('payroll-template.xlsx');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/requests.php';