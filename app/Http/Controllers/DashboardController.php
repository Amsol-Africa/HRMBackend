<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use App\Models\Business;
use App\Models\Industry;
use App\Models\Department;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Models\PayrollFormula;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    function index(Request $request){

        // show(session('managing_business')); die;

        $cards = [
            [
                'title' => 'Total Employees',
                'icon' => 'fa-sharp fa-solid fa-user',
                'value' => 313,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+10%',
                'time_period' => 'Year',
            ],
            [
                'title' => 'On Leave Employees',
                'icon' => 'fa-sharp fa-solid fa-user-group',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Projects',
                'icon' => 'fa-sharp fa-solid fa-gear',
                'value' => 313,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+5.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Complete Projects',
                'icon' => 'fa-light fa-badge-check',
                'value' => 150,
                'trend_class' => 'price-decrease',
                'trend_icon' => 'fa-arrow-down',
                'trend_value' => '+5.5%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Clients',
                'icon' => 'fa-sharp fa-solid fa-users',
                'value' => 151,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Revenues',
                'icon' => 'fa-solid fa-arrow-up-right-dots',
                'value' => '$55',
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Jobs',
                'icon' => 'fa-sharp fa-light fa-suitcase',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
            [
                'title' => 'Total Tickets',
                'icon' => 'fa-solid fa-ticket',
                'value' => 55,
                'trend_class' => 'price-increase',
                'trend_icon' => 'fa-arrow-up',
                'trend_value' => '+2.15%',
                'time_period' => 'Month',
            ],
        ];

        return view('business.index', compact('cards'));
    }

    function clients(Request $request) {
        $page = 'Clients';
        $description = '';
        $business = Business::findBySlug(session('active_business_slug'));
        $clientBusinesses = $business->managedBusinesses;
        return view('clients.index', compact('page', 'description', 'clientBusinesses'));
    }
    function requestAccess(Request $request) {
        $page = 'Request Access';
        $description = 'Choose this option if there is another AMSOL account you would like to manage. A request email will be sent to the email address you provide, allowing the account owner to grant access to the system.';
        return view('clients.access', compact('page', 'description'));
    }
    function grantAccess(Request $request) {
        $page = 'Grant Access';
        $description = 'Select this option if you wish to grant access to your AMSOL account to another user. You will need to confirm their email address, and they will receive an email with access details.';
        $modules = Module::all();
        return view('clients.access', compact('page', 'description', 'modules'));
    }
    function organizationSetup(Request $request) {
        $page = 'Organization Setup';
        $description = '';
        $industries = Industry::all();
        return view('business.setup', compact('page', 'description', 'industries'));
    }
    function departments(Request $request) {
        $page = 'Departments';
        $description = 'Manage and organize all departments within your business. View, create, and update departmental information to streamline operations.';
        return view('departments.index', compact('page', 'description'));
    }
    function jobCategories(Request $request) {
        $page = 'Job Categories';
        $description = '';
        return view('job-categories.index', compact('page', 'description'));
    }
    public function createEmployees(Request $request)
    {
        $page = 'Create New Employee';
        $description = 'Fill out the form below to create a new employee record.';
        $business = Business::findBySlug(session('active_business_slug'));
        $departments = $business->departments;
        $job_categories = $business->job_categories;
        $shifts = $business->shifts;
        $roles = Role::where('name', '!=', 'admin')->get(); // Exclude roles with name 'admin'
        return view('employees.create', compact('page', 'description', 'departments', 'job_categories', 'shifts', 'roles'));
    }
    public function editEmployees(Request $request, User $user)
    {
        $page = 'Update Employee - '.$user->name;
        $description = 'Fill out the form below to update employee record.';
        $departments = auth()->user()->business->departments;
        $roles = Role::where('name', '!=', 'admin')->get(); // Exclude roles with name 'admin'
        return view('employees.create', compact('page', 'description', 'departments', 'roles'));
    }
    public function employeeDetails(Request $request, $business_slug, $user_id)
    {

        $user = User::find($user_id);
        $page = 'About - '.$user->name;
        $description = '';
        return view('employees.show', compact('page', 'user'));
    }
    public function importEmployees(Request $request)
    {
        $page = 'Import Employees';
        $description = '';
        return view('employees.import', compact('page', 'description'));
    }
    public function employees(Request $request)
    {
        $page = 'Employee List';
        $description = 'Here is a list of all employees in the system. You can view, edit, or delete records.';
        $departments = auth()->user()->business->departments;
        return view('employees.index', compact('page', 'description', 'departments'));
    }
    public function shifts(Request $request)
    {
        $page = 'Shifts';
        $description = '';
        return view('shifts.index', compact('page', 'description'));
    }


    public function payrollFormula(Request $request)
    {
        $page = 'Payroll Formula';
        $description = '';
        return view('payroll.formula', compact('page', 'description'));
    }
    public function createPayrollFormula(Request $request)
    {
        $page = 'Create Payroll Formula';
        $description = '';
        return view('payroll.create-formula', compact('page', 'description'));
    }


    public function relief(Request $request)
    {
        $page = 'Reliefs';
        $description = '';
        return view('relief.index', compact('page', 'description'));
    }

    public function createRelief(Request $request)
    {
        $page = 'Create Relief';
        $description = '';
        return view('relief.create', compact('page', 'description'));
    }

    public function deductions(Request $request)
    {
        $page = 'Payroll Deductions';
        $description = '';
        return view('deductions.index', compact('page', 'description'));
    }

    public function createDeductions(Request $request)
    {
        $page = 'Register Payroll Deductions';
        $description = '';
        $formulas = PayrollFormula::all();
        return view('deductions.create', compact('page', 'description', 'formulas'));
    }


    //Leave management
    public function requestLeave(Request $request)
    {
        $page = 'Leave Applications';
        $description = '';
        return view('leave.create', compact('page', 'description'));
    }
    public function leaveApplications(Request $request)
    {
        $page = 'Leave Applications';
        $description = '';
        return view('leave.index', compact('page', 'description'));
    }
    public function leaveTypes(Request $request)
    {
        $page = 'Leave Types';
        $description = '';
        $departments = Department::all();
        $job_categories = JobCategory::all();
        return view('leave.types', compact('page', 'description', 'departments', 'job_categories'));
    }
    public function leavePeriods(Request $request)
    {
        $page = 'Leave Periods';
        $description = '';
        return view('leave.periods', compact('page', 'description'));
    }
    public function leaveEntitlement(Request $request)
    {
        $page = 'Leave Entitlement';
        $business = Business::findBySlug(session('active_business_slug'));
        $description = '';
        $employees = $business->employees;
        $leaveTypes = $business->leaveTypes;
        $leavePeriods = $business->leavePeriods;
        $departments = $business->departments;
        $jobCategories = $business->job_categories;
        return view('leave.entitlement', compact('page', 'description', 'employees', 'leaveTypes', 'leavePeriods', 'departments', 'jobCategories'));
    }
    public function leaveSettings(Request $request)
    {
        $page = 'Leave Settings';
        $description = '';
        return view('leave.settings', compact('page', 'description'));
    }
    public function leaveReports(Request $request)
    {
        $page = 'Leave Reports';
        $description = '';
        return view('leave.reports', compact('page', 'description'));
    }


}
