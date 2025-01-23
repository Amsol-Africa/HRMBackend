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



    Route::middleware(['role:business_owner'])->name('business.')->prefix('business/{business:slug}')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/clients', [DashboardController::class, 'clients'])->name('clients.index');
        Route::get('/departments', [DashboardController::class, 'departments'])->name('departments.index');
        Route::get('/employees/register', [DashboardController::class, 'createEmployees'])->name('employees.create');
        Route::get('/employees', [DashboardController::class, 'employees'])->name('employees.index');
        Route::get('/employees/edit/{employee}', [DashboardController::class, 'editEmployees'])->name('employees.edit');
        Route::get('/employees/import', [DashboardController::class, 'importEmployees'])->name('employees.import');
        Route::get('/job-categories', [DashboardController::class, 'jobCategories'])->name('job-categories.index');
        Route::get('/shifts', [DashboardController::class, 'shifts'])->name('shifts.index');

        Route::get('/payroll/formula/create', [DashboardController::class, 'createPayrollFormula'])->name('payroll.formula.create');
        Route::get('/payroll/formula', [DashboardController::class, 'payrollFormula'])->name('payroll.formula');
        Route::get('/payroll/deductions', [DashboardController::class, 'payrollDeductions'])->name('payroll.deductions');
        Route::get('/payroll/pay-grades', [DashboardController::class, 'payrollPayGrades'])->name('payroll.pay-grades');

        Route::get('/relief/create', [DashboardController::class, 'createRelief'])->name('relief.create');
        Route::get('/relief', [DashboardController::class, 'relief'])->name('relief.index');

        Route::get('/deductions', [DashboardController::class, 'deductions'])->name('deductions.index');
        Route::get('/deductions/create', [DashboardController::class, 'createDeductions'])->name('deductions.create');

        //leave management
        Route::get('/leave/applications', [DashboardController::class, 'leaveApplications'])->name('leave.index');
        Route::get('/leave/types', [DashboardController::class, 'leaveTypes'])->name('leave.types');
        Route::get('/leave/periods', [DashboardController::class, 'leavePeriods'])->name('leave.periods');
        Route::get('/leave/entitlement', [DashboardController::class, 'leaveEntitlement'])->name('leave.entitlement');
        Route::get('/leave/settings', [DashboardController::class, 'leaveSettings'])->name('leave.settings');
        Route::get('/leave/reports', [DashboardController::class, 'leaveReports'])->name('leave.reports');



    });

});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/requests.php';
