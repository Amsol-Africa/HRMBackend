<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Shift;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Department;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    use HandleTransactions;
    public function fetch(Request $request)
    {
        $user = $request->user();
        $business = $user->business;

        $employees = Employee::where('business_id', $business->id)->with(['employmentDetails', 'nextOfKin', 'paymentDetails', 'contactDetails'])->get();
        $employee_cards = view('employees._cards', compact('employees'))->render();
        return RequestResponse::ok('Ok', $employee_cards);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Personal Information
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'national_id' => 'required|string|unique:employees,national_id',
            'tax_no' => 'required|string|max:20',
            'nhif_no' => 'required|string|max:20',
            'nssf_no' => 'required|string|max:20',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',

            // Contact Information
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'phone_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',

            // Next of Kin
            'next_of_kin' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:100',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_phone_code' => 'required|string|max:10',

            // Passport Information
            'passport_no' => 'nullable|string|max:20',
            'passport_issue_date' => 'nullable|date|before:today',
            'passport_expiry_date' => 'nullable|date|after:passport_issue_date',

            // Work Information
            'employee_code' => 'required|string|unique:employees,employee_code|max:50',
            'department' => 'required|string|exists:departments,slug',
            'job_category' => 'required|string|exists:job_categories,slug',
            'employment_date' => 'required|date|before_or_equal:today',
            'probation_end_date' => 'nullable|date|after:employment_date',
            'contract_end_date' => 'nullable|date|after_or_equal:probation_end_date',
            'retirement_date' => 'nullable|date|after:employment_date',
            'shift' => 'required|string|exists:shifts,slug',
            'employment_status' => 'required|string|max:50',
            'job_description' => 'required|string',
            'basic_salary' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',

            // Payment Details
            'payment_mode' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:employee_payment_details,account_number|max:50',
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:20',
            'bank_branch' => 'required|string|max:255',
            'bank_branch_code' => 'required|string|max:20',

            // Work Contact
            'work_phone_no' => 'required|string|max:20',
            'work_email' => 'required|string|email|unique:employee_contact_details,work_email',

            // Miscellaneous
            'email_signature' => 'required|string|max:255',
            'profile_picture' => 'nullable|file|image|max:2048',
            'cv_attachments' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'academic_files' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            // Authentication
            'password' => 'required|string|min:8',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $user = $request->user();
            $business_slug = session('active_business_slug');
            $business = Business::findBySlug($business_slug);

            $department = Department::findBySlug($validatedData['department']);
            $phoneNumber = "+{$request->code}{$request->phone}";
            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            $name = $validatedData['first_name'] . ' ' . $validatedData['middle_name'] . ' ' . $validatedData['last_name'];

            $user = User::create([
                'name' => $name,
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $phoneNumber,
                'code' => $validatedData['phone_code'],
                'country' => $validatedData['country'],
            ]);

            $user->setStatus(Status::ACTIVE);

            $user->assignRole('employee');


            $employee = $business->employees()->create([
                'user_id' => $user->id,
                'employee_code' => $validatedData['employee_code'],
                'department_id' => $department->id,
                // 'business_id' => $business->id,

                'gender' => $validatedData['gender'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'marital_status' => $validatedData['marital_status'],
                'national_id' => $validatedData['national_id'],

                'tax_no' => $validatedData['tax_no'],
                'nhif_no' => $validatedData['nhif_no'],
                'nssf_no' => $validatedData['nssf_no'],
                'blood_group' => $validatedData['blood_group'],
                'passport_no' => $validatedData['passport_no'],

                'passport_issue_date' => $validatedData['passport_issue_date'],
                'passport_expiry_date' => $validatedData['passport_expiry_date'],
            ]);

            // Save next of kin details
            $employee->nextOfKin()->create([
                'name' => $validatedData['next_of_kin'],
                'relationship' => $validatedData['next_of_kin_relationship'],
                'phone' => $validatedData['next_of_kin_phone'],
                'code' => $validatedData['next_of_kin_phone_code'],
            ]);

            // Save employment details
            $employee->employmentDetails()->create([
                'department_id' => Department::where('slug', $validatedData['department'])->firstOrFail()->id,
                'job_category_id' => JobCategory::where('slug', $validatedData['job_category'])->firstOrFail()->id,
                'shift_id' => Shift::where('slug', $validatedData['shift'])->firstOrFail()->id,
                'employment_date' => $validatedData['employment_date'],
                'probation_end_date' => $validatedData['probation_end_date'],
                'contract_end_date' => $validatedData['contract_end_date'],
                'retirement_date' => $validatedData['retirement_date'],
                'employment_status' => $validatedData['employment_status'],
                'job_description' => $validatedData['job_description'],
            ]);

            // Save payment / salary details
            $employee->paymentDetails()->create([
                'basic_salary' => $validatedData['basic_salary'],
                'currency' => $validatedData['currency'],
                'payment_mode' => $validatedData['payment_mode'],
                'account_name' => $validatedData['account_name'],
                'account_number' => $validatedData['account_number'],
                'bank_name' => $validatedData['bank_name'],
                'bank_code' => $validatedData['bank_code'],
                'bank_branch' => $validatedData['bank_branch'],
                'bank_branch_code' => $validatedData['bank_branch_code'],
            ]);

            // Save contact details
            $employee->contactDetails()->create([
                'work_phone' => $validatedData['work_phone_no'],
                'work_phone_code' => $validatedData['phone_code'],
                'work_email' => $validatedData['work_email'],
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'postal_code' => $validatedData['postal_code'],
                'country' => $validatedData['country'],
                'email_signature' => $validatedData['email_signature'],
            ]);

            // Save uploaded files if any
            $request->hasFile('profile_picture')
                ? $user->addMediaFromRequest('profile_picture')->toMediaCollection('avatars')
                : $user->addMediaFromBase64(createAvatarImageFromName($name))->toMediaCollection('avatars');

            if ($request->hasFile('cv_attachments')) {
                $employee->addMedia($request->file('cv_attachments'))->toMediaCollection('cv_attachments');
            }
            if ($request->hasFile('academic_files')) {
                $employee->addMedia($request->file('academic_files'))->toMediaCollection('academic_files');
            }

            return RequestResponse::created('Employee Added successfully.');
        });
    }
}
