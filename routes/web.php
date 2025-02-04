<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;


Route::middleware(['auth'])->group(function () {

    //setup busines & modules
    Route::name('setup.')->prefix('setup')->group(function () {
        Route::get('business', [BusinessController::class, 'create'])->name('business');
        Route::get('modules', [ModuleController::class, 'create'])->name('modules');
    });


    Route::middleware(['role:business-admin'])->name('business.')->prefix('business/{business:slug}')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/clients', [DashboardController::class, 'clients'])->name('clients.index');
        Route::get('/clients/request-access', [DashboardController::class, 'requestAccess'])->name('clients.request-access');
        Route::get('/clients/grant-access', [DashboardController::class, 'grantAccess'])->name('clients.grant-access');
        Route::get('/organization-setup', [DashboardController::class, 'organizationSetup'])->name('organization-setup');
        Route::get('/pay-schedule', [DashboardController::class, 'paySchedule'])->name('pay-schedule');

        Route::get('/departments', [DashboardController::class, 'departments'])->name('departments.index');
        Route::get('/employees/register', [DashboardController::class, 'createEmployees'])->name('employees.create');
        Route::get('/employees', [DashboardController::class, 'employees'])->name('employees.index');
        Route::get('/employees/edit/{employee}', [DashboardController::class, 'editEmployees'])->name('employees.edit');
        Route::get('/employees/details/{employee}', [DashboardController::class, 'employeeDetails'])->name('employees.details');
        Route::get('/employees/import', [DashboardController::class, 'importEmployees'])->name('employees.import');
        Route::get('/job-categories', [DashboardController::class, 'jobCategories'])->name('job-categories.index');
        Route::get('/shifts', [DashboardController::class, 'shifts'])->name('shifts.index');

        Route::get('/payroll/formula/create', [DashboardController::class, 'createPayrollFormula'])->name('payroll.formula.create');
        Route::get('/payroll/formula', [DashboardController::class, 'payrollFormula'])->name('payroll.formula');
        Route::get('/payroll/deductions', [DashboardController::class, 'payrollDeductions'])->name('payroll.deductions');
        Route::get('/payroll/pay-grades', [DashboardController::class, 'payrollPayGrades'])->name('payroll.pay-grades');
        Route::get('/payroll/process', [DashboardController::class, 'processPayrolls'])->name('payroll.process');

        Route::get('/relief/create', [DashboardController::class, 'createRelief'])->name('relief.create');
        Route::get('/relief', [DashboardController::class, 'relief'])->name('relief.index');

        Route::get('/deductions', [DashboardController::class, 'deductions'])->name('deductions.index');
        Route::get('/deductions/create', [DashboardController::class, 'createDeductions'])->name('deductions.create');

        Route::get('/allowances', [DashboardController::class, 'allowances'])->name('allowances.index');
        Route::get('/allowances/create', [DashboardController::class, 'createAllowances'])->name('allowances.create');

        //leave management
        Route::get('/leave/requests/create', [DashboardController::class, 'requestLeave'])->name('leave.create');
        Route::get('/leave/requests', [DashboardController::class, 'leaveApplications'])->name('leave.index');
        Route::get('/leave/types', [DashboardController::class, 'leaveTypes'])->name('leave.types');
        Route::get('/leave/periods', [DashboardController::class, 'leavePeriods'])->name('leave.periods');
        Route::get('/leave/entitlement', [DashboardController::class, 'leaveEntitlement'])->name('leave.entitlement');
        Route::get('/leave/settings', [DashboardController::class, 'leaveSettings'])->name('leave.settings');
        Route::get('/leave/reports', [DashboardController::class, 'leaveReports'])->name('leave.reports');



    });


    Route::middleware(['role:business-employee'])->name('myaccount.')->prefix('myaccount/{business:slug}')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/requests.php';
