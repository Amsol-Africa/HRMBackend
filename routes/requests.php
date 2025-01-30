<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeaveEntitlementController;
use App\Http\Controllers\ReliefController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\LeavePeriodController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeListController;
use App\Http\Controllers\PayrollFormulaController;

Route::middleware(['auth'])->group(function () {

    //manage businesses
    Route::name('businesses.')->prefix('businesses')->group(function () {
        Route::post('store', [BusinessController::class, 'store'])->name('store');
        Route::post('fetch', [BusinessController::class, 'fetch'])->name('fetch');
        Route::post('destroy', [BusinessController::class, 'destroy'])->name('destroy');
        Route::post('update', [BusinessController::class, 'update'])->name('update');
        Route::post('modules/store', [BusinessController::class, 'saveModules'])->name('modules.store');
    });
    //manage job categories
    Route::name('job-categories.')->prefix('job-categories')->group(function () {
        Route::post('edit', [JobCategoryController::class, 'edit'])->name('edit');
        Route::post('store', [JobCategoryController::class, 'store'])->name('store');
        Route::post('fetch', [JobCategoryController::class, 'fetch'])->name('fetch');
        Route::post('delete', [JobCategoryController::class, 'destroy'])->name('delete');
        Route::post('update', [JobCategoryController::class, 'update'])->name('update');
    });
    //manage departments
    Route::name('departments.')->prefix('departments')->group(function () {
        Route::post('edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::post('store', [DepartmentController::class, 'store'])->name('store');
        Route::post('fetch', [DepartmentController::class, 'fetch'])->name('fetch');
        Route::post('delete', [DepartmentController::class, 'destroy'])->name('delete');
        Route::post('update', [DepartmentController::class, 'update'])->name('update');
    });
    //manage shifts
    Route::name('shifts.')->prefix('shifts')->group(function () {
        Route::post('edit', [ShiftController::class, 'edit'])->name('edit');
        Route::post('store', [ShiftController::class, 'store'])->name('store');
        Route::post('fetch', [ShiftController::class, 'fetch'])->name('fetch');
        Route::post('destroy', [ShiftController::class, 'destroy'])->name('destroy');
        Route::post('update', [ShiftController::class, 'update'])->name('update');
    });
    //manage payroll formulas
    Route::name('payroll-formulas.')->prefix('payroll-formulas')->group(function () {
        Route::post('edit', [PayrollFormulaController::class, 'edit'])->name('edit');
        Route::post('store', [PayrollFormulaController::class, 'store'])->name('store');
        Route::post('fetch', [PayrollFormulaController::class, 'fetch'])->name('fetch');
        Route::post('delete', [PayrollFormulaController::class, 'destroy'])->name('delete');
        Route::post('update', [PayrollFormulaController::class, 'update'])->name('update');
        Route::post('show', [PayrollFormulaController::class, 'show'])->name('show');
    });
    //manage employees
    Route::name('employees.')->prefix('employees')->group(function () {
        Route::post('edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::post('store', [EmployeeController::class, 'store'])->name('store');
        Route::post('fetch', [EmployeeController::class, 'fetch'])->name('fetch');
        Route::post('filter', [EmployeeController::class, 'filter'])->name('filter');
        Route::post('destroy', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::post('update', [EmployeeController::class, 'update'])->name('update');
    });
    //manage leaves
    Route::name('leave-types.')->prefix('leave-types')->group(function () {
        Route::post('edit', [LeaveTypeController::class, 'edit'])->name('edit');
        Route::post('store', [LeaveTypeController::class, 'store'])->name('store');
        Route::post('fetch', [LeaveTypeController::class, 'fetch'])->name('fetch');
        Route::post('show', [LeaveTypeController::class, 'show'])->name('show');
        Route::post('destroy', [LeaveTypeController::class, 'destroy'])->name('destroy');
        Route::post('update', [LeaveTypeController::class, 'update'])->name('update');
        Route::post('suggestions', [LeaveTypeListController::class, 'suggestions'])->name('suggestions');
    });
    Route::name('leave.')->prefix('leave')->group(function () {
        Route::post('edit', [LeaveRequestController::class, 'edit'])->name('edit');
        Route::post('store', [LeaveRequestController::class, 'store'])->name('store');
        Route::post('fetch', [LeaveRequestController::class, 'fetch'])->name('fetch');
        Route::post('destroy', [LeaveRequestController::class, 'destroy'])->name('destroy');
        Route::post('update', [LeaveRequestController::class, 'update'])->name('update');
    });
    Route::name('leave-periods.')->prefix('leave-periods')->group(function () {
        Route::post('edit', [LeavePeriodController::class, 'edit'])->name('edit');
        Route::post('store', [LeavePeriodController::class, 'store'])->name('store');
        Route::post('fetch', [LeavePeriodController::class, 'fetch'])->name('fetch');
        Route::post('show', [LeavePeriodController::class, 'show'])->name('show');
        Route::post('destroy', [LeavePeriodController::class, 'destroy'])->name('destroy');
        Route::post('update', [LeavePeriodController::class, 'update'])->name('update');
    });
    Route::name('leave-entitlements.')->prefix('leave-entitlements')->group(function () {
        Route::post('edit', [LeaveEntitlementController::class, 'edit'])->name('edit');
        Route::post('store', [LeaveEntitlementController::class, 'store'])->name('store');
        Route::post('fetch', [LeaveEntitlementController::class, 'fetch'])->name('fetch');
        Route::post('show', [LeaveEntitlementController::class, 'show'])->name('show');
        Route::post('destroy', [LeaveEntitlementController::class, 'destroy'])->name('destroy');
        Route::post('update', [LeaveEntitlementController::class, 'update'])->name('update');
    });
    Route::name('reliefs.')->prefix('reliefs')->group(function () {
        Route::post('edit', [ReliefController::class, 'edit'])->name('edit');
        Route::post('store', [ReliefController::class, 'store'])->name('store');
        Route::post('fetch', [ReliefController::class, 'fetch'])->name('fetch');
        Route::post('show', [ReliefController::class, 'show'])->name('show');
        Route::post('destroy', [ReliefController::class, 'destroy'])->name('destroy');
        Route::post('update', [ReliefController::class, 'update'])->name('update');
    });
    Route::name('client-businesses.')->prefix('client-businesses')->group(function () {
        Route::post('request-access', [ClientController::class, 'requestAccess'])->name('request-access');
        Route::post('grant-access', [ClientController::class, 'grantAccess'])->name('grant-access');
        Route::post('fetch', [ClientController::class, 'fetch'])->name('fetch');
        Route::post('access', [ClientController::class, 'impersonateManagedBusiness'])->name('access');
        Route::post('destroy', [ClientController::class, 'destroy'])->name('destroy');
        Route::post('update', [ClientController::class, 'update'])->name('update');
    });
});
