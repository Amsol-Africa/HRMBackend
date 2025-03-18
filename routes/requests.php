<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReliefController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\LeavePeriodController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeListController;
use App\Http\Controllers\PayrollFormulaController;
use App\Http\Controllers\LeaveEntitlementController;
use App\Http\Controllers\EmployeeDeductionController;

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
        Route::post('delete', [ShiftController::class, 'destroy'])->name('delete');
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
        Route::post('create', [PayrollFormulaController::class, 'create'])->name('create');
    });
    //manage deductions
    Route::name('deductions.')->prefix('deductions')->group(function () {
        Route::post('edit', [DeductionController::class, 'edit'])->name('edit');
        Route::post('store', [DeductionController::class, 'store'])->name('store');
        Route::post('fetch', [DeductionController::class, 'fetch'])->name('fetch');
        Route::post('delete', [DeductionController::class, 'destroy'])->name('delete');
        Route::post('update', [DeductionController::class, 'update'])->name('update');
        Route::post('show', [DeductionController::class, 'show'])->name('show');
    });
    //manage empliyee deductions
    Route::name('employee-deductions.')->prefix('employee-deductions')->group(function () {
        Route::post('create', [EmployeeDeductionController::class, 'create'])->name('create');
        Route::post('edit', [EmployeeDeductionController::class, 'edit'])->name('edit');
        Route::post('store', [EmployeeDeductionController::class, 'store'])->name('store');
        Route::post('fetch', [EmployeeDeductionController::class, 'fetch'])->name('fetch');
        Route::post('delete', [EmployeeDeductionController::class, 'destroy'])->name('delete');
        Route::post('update', [EmployeeDeductionController::class, 'update'])->name('update');
        Route::post('show', [EmployeeDeductionController::class, 'show'])->name('show');
    });
    //manage employees
    Route::name('employees.')->prefix('employees')->group(function () {
        Route::post('edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::post('store', [EmployeeController::class, 'store'])->name('store');
        Route::post('fetch', [EmployeeController::class, 'fetch'])->name('fetch');
        Route::post('filter', [EmployeeController::class, 'filter'])->name('filter');
        Route::post('delete', [EmployeeController::class, 'destroy'])->name('delete');
        Route::post('update', [EmployeeController::class, 'update'])->name('update');
        Route::post('list', [EmployeeController::class, 'list'])->name('list');
    });
    //manage leaves
    Route::name('leave-types.')->prefix('leave-types')->group(function () {
        Route::post('edit', [LeaveTypeController::class, 'edit'])->name('edit');
        Route::post('store', [LeaveTypeController::class, 'store'])->name('store');
        Route::post('fetch', [LeaveTypeController::class, 'fetch'])->name('fetch');
        Route::post('show', [LeaveTypeController::class, 'show'])->name('show');
        Route::post('destroy', [LeaveTypeController::class, 'destroy'])->name('delete');
        Route::post('update', [LeaveTypeController::class, 'update'])->name('update');
        Route::post('suggestions', [LeaveTypeListController::class, 'suggestions'])->name('suggestions');
    });
    Route::name('leave.')->prefix('leave')->group(function () {
        Route::post('edit', [LeaveRequestController::class, 'edit'])->name('edit');
        Route::post('store', [LeaveRequestController::class, 'store'])->name('store');
        Route::post('fetch', [LeaveRequestController::class, 'fetch'])->name('fetch');
        Route::post('delete', [LeaveRequestController::class, 'destroy'])->name('delete');
        Route::post('update', [LeaveRequestController::class, 'update'])->name('update');
        Route::post('status', [LeaveRequestController::class, 'status'])->name('status');
    });
    Route::name('leave-periods.')->prefix('leave-periods')->group(function () {
        Route::post('edit', [LeavePeriodController::class, 'edit'])->name('edit');
        Route::post('store', [LeavePeriodController::class, 'store'])->name('store');
        Route::post('fetch', [LeavePeriodController::class, 'fetch'])->name('fetch');
        Route::post('show', [LeavePeriodController::class, 'show'])->name('show');
        Route::post('delete', [LeavePeriodController::class, 'destroy'])->name('delete');
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
    Route::name('locations.')->prefix('locations')->group(function () {
        Route::post('edit', [LocationController::class, 'edit'])->name('edit');
        Route::post('store', [LocationController::class, 'store'])->name('store');
        Route::post('fetch', [LocationController::class, 'fetch'])->name('fetch');
        Route::post('show', [LocationController::class, 'show'])->name('show');
        Route::post('delete', [LocationController::class, 'destroy'])->name('delete');
        Route::post('update', [LocationController::class, 'update'])->name('update');
    });
    Route::name('payroll.')->prefix('payroll')->group(function () {
        Route::post('edit', [PayrollController::class, 'edit'])->name('edit');
        Route::post('store', [PayrollController::class, 'store'])->name('store');
        Route::post('fetch', [PayrollController::class, 'fetch'])->name('fetch');
        Route::post('slips', [PayrollController::class, 'slips'])->name('slips');
        Route::post('slips/show', [PayrollController::class, 'showSlip'])->name('slips.show');
        Route::post('slips/download', [PayrollController::class, 'downloadSlip'])->name('slips.download');
        Route::post('slips/email', [PayrollController::class, 'emailSlip'])->name('slips.email');
        Route::post('show', [PayrollController::class, 'show'])->name('show');
        Route::post('destroy', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('update', [PayrollController::class, 'update'])->name('update');
    });
    Route::name('advances.')->prefix('advances')->group(function () {
        Route::post('edit', [AdvanceController::class, 'edit'])->name('edit');
        Route::post('store', [AdvanceController::class, 'store'])->name('store');
        Route::post('fetch', [AdvanceController::class, 'fetch'])->name('fetch');
        Route::post('delete', [AdvanceController::class, 'destroy'])->name('delete');
        Route::post('update', [AdvanceController::class, 'update'])->name('update');
    });
    Route::name('loans.')->prefix('loans')->group(function () {
        Route::post('edit', [LoanController::class, 'edit'])->name('edit');
        Route::post('store', [LoanController::class, 'store'])->name('store');
        Route::post('fetch', [LoanController::class, 'fetch'])->name('fetch');
        Route::post('delete', [LoanController::class, 'destroy'])->name('delete');
        Route::post('update', [LoanController::class, 'update'])->name('update');
    });

    // Job Postings Management
    Route::name('job-posts.')->prefix('job-posts')->group(function () {
        Route::post('edit', [JobPostController::class, 'edit'])->name('edit');
        Route::post('store', [JobPostController::class, 'store'])->name('store');
        Route::post('fetch', [JobPostController::class, 'fetch'])->name('fetch');
        Route::post('show', [JobPostController::class, 'show'])->name('show');
        Route::post('destroy', [JobPostController::class, 'destroy'])->name('destroy');
        Route::post('update', [JobPostController::class, 'update'])->name('update');
    });

    // Job Applications Management
    Route::name('applications.')->prefix('applications')->group(function () {
        Route::post('edit', [ApplicationController::class, 'edit'])->name('edit');
        Route::post('store', [ApplicationController::class, 'store'])->name('store');
        Route::post('fetch', [ApplicationController::class, 'fetch'])->name('fetch');
        Route::post('destroy', [ApplicationController::class, 'destroy'])->name('destroy');
        Route::post('update', [ApplicationController::class, 'update'])->name('update');
    });

    // Interview Scheduling
    Route::name('interviews.')->prefix('interviews')->group(function () {
        Route::post('edit', [InterviewController::class, 'edit'])->name('edit');
        Route::post('store', [InterviewController::class, 'store'])->name('store');
        Route::post('fetch', [InterviewController::class, 'fetch'])->name('fetch');
        Route::post('show', [InterviewController::class, 'show'])->name('show');
        Route::post('destroy', [InterviewController::class, 'destroy'])->name('destroy');
        Route::post('update', [InterviewController::class, 'update'])->name('update');
        Route::post('reschedule', [InterviewController::class, 'reschedule'])->name('reschedule');
        Route::post('cancel', [InterviewController::class, 'cancel'])->name('cancel');
    });

    // Applicant Management
    Route::name('applicants.')->prefix('applicants')->group(function () {
        Route::post('edit', [ApplicantController::class, 'edit'])->name('edit');
        Route::post('store', [ApplicantController::class, 'store'])->name('store');
        Route::post('fetch', [ApplicantController::class, 'fetch'])->name('fetch');
        Route::post('show', [ApplicantController::class, 'show'])->name('show');
        Route::post('destroy', [ApplicantController::class, 'destroy'])->name('destroy');
        Route::post('update', [ApplicantController::class, 'update'])->name('update');
    });

    // Tasks
    Route::name('tasks.')->prefix('tasks')->group(function () {
        Route::post('fetch', [TaskController::class, 'fetch'])->name('fetch');
        Route::post('edit', [TaskController::class, 'edit'])->name('edit');
        Route::post('store', [TaskController::class, 'store'])->name('store');
        Route::post('update/{task}', [TaskController::class, 'update'])->name('update');
        Route::post('destroy', [TaskController::class, 'destroy'])->name('destroy');
        Route::post('progress', [TaskController::class, 'progress'])->name('progress');
        Route::post('timelines', [TaskController::class, 'timelines'])->name('timelines');
    });

    // Applicant Management
    Route::name('attendances.')->prefix('attendances')->group(function () {
        Route::post('clockin', [AttendanceController::class, 'clockIn'])->name('clockin');
        Route::post('clockout', [AttendanceController::class, 'clockOut'])->name('clockout');
        Route::post('fetch', [AttendanceController::class, 'fetch'])->name('fetch');
        Route::post('show', [AttendanceController::class, 'show'])->name('show');
        Route::post('destroy', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::post('update', [AttendanceController::class, 'update'])->name('update');
        Route::post('clockins', [AttendanceController::class, 'clockIns'])->name('clockins');
        Route::post('monthly', [AttendanceController::class, 'monthly'])->name('monthly');
    });

    // Applicant Management
    Route::name('overtime.')->prefix('overtime')->group(function () {
        Route::post('store', [OvertimeController::class, 'store'])->name('store');
        Route::post('destroy', [OvertimeController::class, 'destroy'])->name('destroy');
        Route::post('edit', [OvertimeController::class, 'edit'])->name('edit');
        Route::post('fetch', [OvertimeController::class, 'fetch'])->name('fetch');
    });

    // Profile
    Route::name('profile.')->prefix('profile')->group(function () {
        Route::post('store', [ProfileController::class, 'store'])->name('store');
        Route::post('destroy', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('password', [ProfileController::class, 'password'])->name('password');
    });

    // trends
    // trends
    Route::name('trends.')->prefix('trends')->group(function () {
        Route::post('payroll', [TrendController::class, 'payroll'])->name('payroll');
        Route::post('attendance', [TrendController::class, 'attendance'])->name('attendance');
        Route::post('leave', [TrendController::class, 'leave'])->name('leave');
        Route::post('loans', [TrendController::class, 'loans'])->name('loans');
    });

    // activities
    Route::name('activities.')->prefix('activities')->group(function () {
        Route::post('fetch', [ActivityLogController::class, 'index'])->name('fetch');
    });

    //print
    Route::get('/payslip/print/{id}', [PayrollController::class, 'printPayslip'])->name('payslip.print');

    // Gen AI
    Route::post('/generate-job-description', [JobPostController::class, 'generateDescription'])->middleware('auth');
});
