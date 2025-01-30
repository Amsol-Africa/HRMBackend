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
use Illuminate\Support\Facades\Log;
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

        $employees = Employee::where('business_id', $business->id)
            ->when($request->name, function ($query, $employeeName) {
                return $query->whereHas('user', function ($query) use ($employeeName) {
                    $query->where('name', 'like', '%' . $employeeName . '%');
                });
            })
            ->when($request->employee_no, function ($query, $employeeNo) {
                return $query->where('employee_code', 'like', '%' . $employeeNo . '%');
            })
            ->when($request->department, function ($query, $employeeDepartment) {
                return $query->whereHas('department', function ($query) use ($employeeDepartment) {
                    $query->where('slug', $employeeDepartment);
                });
            })
            ->when($request->status, function ($query, $employeeStatus) {
                if ($employeeStatus !== 'all') {
                    return $query->whereHas('user', function ($query) use ($employeeStatus) {
                        $query->currentStatus($employeeStatus);  // Assuming this method filters by status.
                    });
                }
            })
            ->when($request->gender, function ($query, $employeeGender) {
                return $query->where('gender', $employeeGender);
            })
            ->get();

        $employee_cards = view('employees._cards', compact('employees'))->render();
        return RequestResponse::ok('Ok', $employee_cards);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Personal Information
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code|max:50',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'phone_code' => 'required|string|max:10',
            'phone_country' => 'required|string',
            'alternate_phone' => 'required|string|max:20',
            'alternate_phone_code' => 'required|string|max:10',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'required|string',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'national_id' => 'required|string|unique:employees,national_id',
            'place_of_issue' => 'required|string',
            'tax_no' => 'required|string|max:20',
            'nhif_no' => 'required|string|max:20',
            'nssf_no' => 'required|string|max:20',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'passport_no' => 'nullable|string|max:20',
            'passport_issue_date' => 'nullable|date|before:today',
            'passport_expiry_date' => 'nullable|date|after:passport_issue_date',
            'address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',

            // Spouse Information
            'spouse_surname_name' => 'required|string',
            'spouse_first_name' => 'required|string',
            'spouse_middle_name' => 'required|string|',
            'spouse_date_of_birth' => 'required|string|',
            'spouse_national_id' => 'required|string',
            'spouse_phone' => 'required|string',
            'spouse_phone_code' => 'required|string',
            'spouse_current_employer' => 'required|string',
            'spouse_postal_address' => 'required|string',
            'spouse_physical_address' => 'required|string',

            // Work Information
            'department' => 'required|string|exists:departments,slug',
            'job_category' => 'required|string|exists:job_categories,slug',
            'employment_date' => 'required|date',
            'probation_end_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'retirement_date' => 'nullable|date',
            'shift' => 'required|string|exists:shifts,slug',
            'employment_term' => 'required|string|max:50',
            'job_description' => 'required|string',

            // Payment Details
            'basic_salary' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'payment_mode' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:employee_payment_details,account_number|max:50',
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:20',
            'bank_branch' => 'required|string|max:255',
            'bank_branch_code' => 'required|string|max:20',

            // Academic & Professional Details
            'certification' => 'required|array',
            'certification.*' => 'required|string|max:255',
            'institution' => 'required|array',
            'institution.*' => 'required|string|max:255',
            'from' => 'required|array',
            'from.*' => 'required|date|before_or_equal:to.*',
            'to' => 'required|array',
            'to.*' => 'required|date|after_or_equal:from.*',

            // Emergency contact validation
            'emmergency_contact_name' => 'required|array',
            'emmergency_contact_name.*' => 'required|string|max:255',
            'emmergency_contact_relationship' => 'required|array',
            'emmergency_contact_relationship.*' => 'required|string|max:255',
            'emmergency_contact_address' => 'required|array',
            'emmergency_contact_address.*' => 'required|string|max:255',
            'emmergency_contact_phone' => 'required|array',
            'emmergency_contact_phone.*' => 'required|string|max:15',
            'emmergency_contact_phone_code' => 'required|array',
            'emmergency_contact_phone_code.*' => 'required|string|max:10',
            'emmergency_contact_phone_country' => 'required|array',
            'emmergency_contact_phone_country.*' => 'required|string|max:100',

            // Validation for family members
            'family_member_name' => 'required|array',
            'family_member_name.*' => 'required|string|max:255',
            'family_member_relationship' => 'required|array',
            'family_member_relationship.*' => 'required|string|max:255',
            'family_member_date_of_birth' => 'required|array',
            'family_member_date_of_birth.*' => 'required|date',
            'family_member_contact_address' => 'required|array',
            'family_member_contact_address.*' => 'required|string|max:255',
            'family_member_contact_phone' => 'required|array',
            'family_member_contact_phone.*' => 'required|string|max:15',
            'family_member_contact_phone_code' => 'required|array',
            'family_member_contact_phone_code.*' => 'required|string|max:10',
            'family_member_contact_phone_country' => 'required|array',
            'family_member_contact_phone_country.*' => 'required|string|max:100',

            // Validation for single entry employment details
            'employer_name' => 'required|string|max:255',
            'business_or_profession' => 'required|string|max:255',
            'employment_address' => 'required|string|max:255',
            'employment_capacity' => 'required|string|max:255',
            'employment_from' => 'required|date',
            'employment_to' => 'nullable|date|after_or_equal:employment_from',
            'reason_for_leaving' => 'nullable|string|max:500',

            // Miscellaneous
            'email_signature' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|file|image|max:2048',
            'cv_attachments' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'academic_files' => 'nullable|file|mimes:pdf,doc,docx,xlsx|max:2048',

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

            $alternatePhoneNumber = "+{$request->alternate_phone_code}{$request->alternate_phone}";
            $spousePhoneNumber = "+{$request->spouse_phone_code}{$request->spouse_phone}";

            $name = $validatedData['first_name'] . ' ' . $validatedData['middle_name'] . ' ' . $validatedData['last_name'];

            $user = User::create([
                'name' => $name,
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $phoneNumber,
                'code' => $validatedData['phone_code'],
                'country' => $validatedData['phone_country'],
            ]);

            $user->setStatus(Status::ACTIVE);

            $user->assignRole('employee');


            $employee = $business->employees()->create([
                'user_id' => $user->id,
                'employee_code' => $validatedData['employee_code'],
                'department_id' => $department->id,
                // 'business_id' => $business->id,

                'gender' => $validatedData['gender'],
                'alternate_phone' => $alternatePhoneNumber,
                'date_of_birth' => $validatedData['date_of_birth'],
                'marital_status' => $validatedData['marital_status'],
                'national_id' => $validatedData['national_id'],
                'place_of_issue' => $validatedData['place_of_issue'],

                'tax_no' => $validatedData['tax_no'],
                'nhif_no' => $validatedData['nhif_no'],
                'nssf_no' => $validatedData['nssf_no'],
                'passport_no' => $validatedData['passport_no'],
                'passport_issue_date' => $validatedData['passport_issue_date'],
                'passport_expiry_date' => $validatedData['passport_expiry_date'],
                'address' => $validatedData['address'],
                'permanent_address' => $validatedData['permanent_address'],
                'blood_group' => $validatedData['blood_group'],

            ]);

            // Save next of kin details
            $employee->spouse()->create([
                'surname' => $validatedData['spouse_surname_name'],
                'first_name' => $validatedData['spouse_first_name'],
                'middle_name' => $validatedData['spouse_middle_name'],
                'date_of_birth' => $validatedData['spouse_date_of_birth'],
                'national_id' => $validatedData['spouse_national_id'],
                'current_employer' => $validatedData['spouse_current_employer'],
                'spouse_contact' => $spousePhoneNumber,
                'spouse_postal_address' => $validatedData['spouse_postal_address'],
                'spouse_physical_address' => $validatedData['spouse_physical_address'],
            ]);

            // Save employment details
            $employee->employmentDetails()->create([
                'department_id' => Department::where('slug', $validatedData['department'])->firstOrFail()->id,
                'job_category_id' => JobCategory::where('slug', $validatedData['job_category'])->firstOrFail()->id,
                'employment_date' => $validatedData['employment_date'],
                'probation_end_date' => $validatedData['probation_end_date'],
                'contract_end_date' => $validatedData['contract_end_date'],
                'retirement_date' => $validatedData['retirement_date'],
                'shift_id' => Shift::where('slug', $validatedData['shift'])->firstOrFail()->id,
                'employment_term' => $validatedData['employment_term'],
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

            // Save Academic & Professional Details
            foreach ($validatedData['certification'] as $index => $certification) {
                $employee->academicDetails()->create([
                    'start_date' => $validatedData['from'][$index],
                    'end_date' => $validatedData['to'][$index],
                    'institution_name' => $validatedData['institution'][$index],
                    'certification_obtained' => $certification,
                ]);
            }

            // Save emergency contacts
            foreach ($validatedData['emmergency_contact_name'] as $index => $contactName) {
                $employee->emergencyContacts()->create([
                    'name' => $contactName,
                    'relationship' => $validatedData['emmergency_contact_relationship'][$index],
                    'contact_address' => $validatedData['emmergency_contact_address'][$index],
                    'telephone' => $validatedData['emmergency_contact_phone'][$index],
                ]);
            }

            // Save each family member
            foreach ($validatedData['family_member_name'] as $index => $name) {
                $employee->familyMembers()->create([
                    'name' => $name,
                    'relationship' => $validatedData['family_member_relationship'][$index],
                    'date_of_birth' => $validatedData['family_member_date_of_birth'][$index],
                    'contact_address' => $validatedData['family_member_contact_address'][$index],
                    'phone' => $validatedData['family_member_contact_phone'][$index],
                    'code' => $validatedData['family_member_contact_phone_code'][$index],
                ]);
            }

            // Create or update the employment information
            $employee->previousEmployment()->create([
                'employer_name' => $validatedData['employer_name'],
                'business_or_profession' => $validatedData['business_or_profession'],
                'address' => $validatedData['employment_address'],
                'capacity_employed' => $validatedData['employment_capacity'],
                'start_date' => $validatedData['employment_from'],
                'end_date' => $validatedData['employment_to'],
                'reason_for_leaving' => $validatedData['reason_for_leaving'],
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
    public function filter(Request $request)
    {
        Log::debug($request->all());

        $validatedData = $request->validate([
            'departments' => 'array|nullable',
            'departments.*' => 'exists:departments,slug',
            'jobCategories' => 'array|nullable',
            'jobCategories.*' => 'exists:job_categories,slug',
            'employmentTerms' => 'array|nullable',
            'employmentTerms.*' => 'in:permanent,contract,temporary,internship',
        ]);

        $business_slug = session('active_business_slug');
        $business = Business::findBySlug($business_slug);

        // Start building the query for employees
        $employeesQuery = $business->employees();

        // Filter by departments if provided
        if (isset($validatedData['departments']) && count($validatedData['departments']) > 0) {
            $employeesQuery->whereHas('department', function ($query) use ($validatedData) {
                $query->whereIn('slug', $validatedData['departments']);
            });
        }

        // Filter by job categories if provided
        if (isset($validatedData['jobCategories']) && count($validatedData['jobCategories']) > 0) {
            $employeesQuery->whereHas('employmentDetails', function ($query) use ($validatedData) {
                $query->whereIn('job_category_id', JobCategory::whereIn('slug', $validatedData['jobCategories'])->pluck('id'));
            });
        }

        // Filter by employment terms if provided
        if (isset($validatedData['employmentTerms']) && count($validatedData['employmentTerms']) > 0) {
            $employeesQuery->whereHas('employmentDetails', function ($query) use ($validatedData) {
                $query->whereIn('employment_term', $validatedData['employmentTerms']);
            });
        }

        // Retrieve filtered employees
        $employees = $employeesQuery->get();

        // Map the employees for the response
        $employeesData = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->name,
                'department' => $employee->department->name ?? 'N/A',
            ];
        });

        return RequestResponse::ok('Ok.', $employeesData);
    }



}
