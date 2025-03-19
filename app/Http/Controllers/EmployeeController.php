<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Shift;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Department;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\EmploymentDetail;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Imports\EmployeesImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployeeController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $user = $request->user();
        $business = Business::findBySlug(session('active_business_slug'));

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
            ->when($request->location, function ($query, $employeeLocation) {
                return $query->whereHas('location', function ($query) use ($employeeLocation) {
                    $query->where('slug', $employeeLocation);
                });
            })
            ->when($request->status, function ($query, $employeeStatus) {
                if ($employeeStatus !== 'all') {
                    return $query->whereHas('user', function ($query) use ($employeeStatus) {
                        $query->currentStatus($employeeStatus);
                    });
                }
            })
            ->when($request->gender, function ($query, $employeeGender) {
                return $query->where('gender', $employeeGender);
            })
            ->get();

        $employee_table = view('employees._table', compact('employees'))->render();

        return RequestResponse::ok('Ok', $employee_table);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'location' => 'nullable|string|exists:locations,slug',
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
            $location = Location::findBySlug($validatedData['location']);

            $department = Department::findBySlug($validatedData['department']);
            $phoneNumber = "+{$request->phone_code}{$request->phone}"; // Fixed $request->code to $request->phone_code
            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            $alternatePhoneNumber = "+{$request->alternate_phone_code}{$request->alternate_phone}";
            $spousePhoneNumber = "+{$request->spouse_phone_code}{$request->spouse_phone}";

            $name = trim($validatedData['first_name'] . ' ' . ($validatedData['middle_name'] ?? '') . ' ' . $validatedData['last_name']);

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
                'location_id' => $location?->id,
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
                'department_id' => $department->id,
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

            return RequestResponse::created('Employee added successfully.');
        });
    }

    public function filter(Request $request)
    {
        Log::debug($request->all());

        $business_slug = session('active_business_slug');
        $business = Business::findBySlug($business_slug);

        if (!$business) {
            return RequestResponse::badRequest('Business not found.', 404);
        }

        $validatedData = $request->validate([
            'locations' => 'array|nullable',
            'locations.*' => [
                function ($attribute, $value, $fail) use ($business_slug) {
                    if ($value !== 'all' && $value !== $business_slug && !Location::where('slug', $value)->exists()) {
                        $fail("The selected location '$value' is invalid.");
                    }
                },
            ],
            'departments' => 'array|nullable',
            'departments.*' => [
                function ($attribute, $value, $fail) {
                    if ($value !== 'all' && !Department::where('slug', $value)->exists()) {
                        $fail("The selected department '$value' is invalid.");
                    }
                },
            ],
            'job_categories' => 'array|nullable',
            'job_categories.*' => [
                function ($attribute, $value, $fail) {
                    if ($value !== 'all' && !JobCategory::where('slug', $value)->exists()) {
                        $fail("The selected job category '$value' is invalid.");
                    }
                },
            ],
            'employment_terms' => 'array|nullable',
            'employment_terms.*' => [
                function ($attribute, $value, $fail) {
                    $validTerms = ['permanent', 'contract', 'temporary', 'internship'];
                    if ($value !== 'all' && !in_array($value, $validTerms)) {
                        $fail("The selected employment term '$value' is invalid.");
                    }
                },
            ],
        ]);

        $query = $business->employees()->with(['user:id,name', 'department:id,name']);

        if (empty($validatedData['locations']) || in_array($business_slug, $validatedData['locations'])) {
            $query->whereNull('location_id');
        }

        if (!empty($validatedData)) {
            if (!empty($validatedData['departments']) && !in_array('all', $validatedData['departments'])) {
                $query->whereHas('department', function ($q) use ($validatedData) {
                    $q->whereIn('slug', $validatedData['departments']);
                });
            }

            if (!empty($validatedData['job_categories']) && !in_array('all', $validatedData['job_categories'])) {
                $jobCategoryIds = JobCategory::whereIn('slug', $validatedData['job_categories'])->pluck('id');
                $query->whereHas('employmentDetails', function ($q) use ($jobCategoryIds) {
                    $q->whereIn('job_category_id', $jobCategoryIds);
                });
            }

            if (!empty($validatedData['locations']) && !in_array('all', $validatedData['locations']) && !in_array($business_slug, $validatedData['locations'])) {
                $locationIds = Location::whereIn('slug', $validatedData['locations'])->pluck('id');
                $query->whereHas('location', function ($q) use ($locationIds) {
                    $q->whereIn('id', $locationIds);
                });
            }

            if (!empty($validatedData['employment_terms']) && !in_array('all', $validatedData['employment_terms'])) {
                $query->whereHas('employmentDetails', function ($q) use ($validatedData) {
                    $q->whereIn('employment_term', $validatedData['employment_terms']);
                });
            }
        }

        $filteredEmployees = $query->get();

        $employeesData = $filteredEmployees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->name ?? 'N/A',
                'department' => $employee->department->name ?? 'N/A',
                'checked' => true,
            ];
        });

        return RequestResponse::ok('Filtered employees retrieved successfully.', $employeesData);
    }

    public function list(Request $request)
    {
        $business_slug = session('active_business_slug');
        $business = Business::findBySlug($business_slug);
        $employees = Employee::with('user')->where('business_id', $business->id)->get();
        return RequestResponse::ok('Ok', $employees);
    }

    public function sendAlert(NotificationService $notificationService)
    {
        $user = User::find(1);
        $preferences = $notificationService->getUserNotificationPreferences($user);
        $channels = $notificationService->filterChannelsByUserPreferences($user, ['mail', 'database', 'slack']);

        $notificationService->sendNotification(
            $user,
            SystemAlertNotification::class,
            ['System maintenance scheduled.', ['details' => 'Server will be down for 2 hours.']],
            [],
            $channels
        );

        return response()->json(['message' => 'Notification sent.']);
    }

    public function setUserPreferences(NotificationService $notificationService)
    {
        $user = auth()->user();
        $notificationService->setUserNotificationPreferences($user, ['email' => false, 'database' => true]);
        return response()->json(['message' => 'User preferences updated.']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        $file = $request->file('file');
        $business = Business::findBySlug(session('active_business_slug'));
        $errors = [];
        $successful = 0;

        // Pre-fetch departments and locations for faster lookups
        $departments = $business->departments()->pluck('id', 'name')->toArray();
        $locations = $business->locations()->pluck('id', 'name')->toArray();

        try {
            DB::beginTransaction();

            $records = Excel::toArray(new EmployeesImport, $file)[0];
            if (empty($records)) {
                return RequestResponse::badRequest('The uploaded XLSX file is empty or invalid.', ['errors' => ['No data found in the file.']]);
            }
            $headers = array_shift($records);
            $records = array_map(function ($row) use ($headers) {
                return array_combine($headers, $row);
            }, $records);

            Log::debug('Import records:', $records);

            if (empty($records)) {
                return RequestResponse::badRequest('No data found in the uploaded file.', ['errors' => ['The file contains no records to import.']]);
            }

            foreach ($records as $index => $row) {
                try {
                    $name = trim("{$row['first_name']} " . ($row['middle_name'] ?? '') . " {$row['last_name']}");
                    $phone = "+{$row['phone_code']}{$row['phone']}";

                    // Validate required fields
                    $validator = Validator::make($row, [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => [
                            'required',
                            'email:rfc,dns',
                            'unique:users,email',
                            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
                        ],
                        'phone' => 'required|numeric|digits_between:9,12',
                        'phone_code' => 'nullable|numeric|digits_between:1,3',
                        'phone_country' => 'nullable|string',
                        'password' => 'nullable|string|min:8',
                        'employee_code' => 'required|string|unique:employees,employee_code|max:50',
                        'gender' => 'required|in:male,female',
                        'date_of_birth' => 'required|date|before:today',
                        'marital_status' => 'nullable|in:single,married,divorced,widowed',
                        'national_id' => 'required|numeric|digits_between:5,10|unique:employees,national_id',
                        'department' => 'required|string',
                        'location' => 'nullable|string',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    // Validate department exists
                    if (!isset($departments[$row['department']])) {
                        $errors[] = "Row " . ($index + 2) . ": Department '{$row['department']}' not found.";
                        continue;
                    }

                    // Validate location exists if provided
                    if (!empty($row['location']) && !isset($locations[$row['location']])) {
                        $errors[] = "Row " . ($index + 2) . ": Location '{$row['location']}' not found.";
                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'name' => $name,
                        'email' => $row['email'],
                        'phone' => $phone,
                        'code' => $row['phone_code'],
                        'country' => $row['phone_country'],
                        'password' => Hash::make($row['password']),
                    ]);
                    $user->setStatus(Status::ACTIVE);
                    $user->assignRole('employee');

                    // Create employee with department and location IDs
                    $employee = $business->employees()->create([
                        'user_id' => $user->id,
                        'employee_code' => $row['employee_code'],
                        'department_id' => $departments[$row['department']],
                        'location_id' => !empty($row['location']) ? $locations[$row['location']] : null,
                        'gender' => $row['gender'],
                        'alternate_phone' => $row['alternate_phone'] ?? null,
                        'date_of_birth' => $row['date_of_birth'],
                        'marital_status' => $row['marital_status'],
                        'national_id' => $row['national_id'],
                        'place_of_issue' => $row['place_of_issue'] ?? null,
                        'tax_no' => $row['tax_no'] ?? null,
                        'nhif_no' => $row['nhif_no'] ?? null,
                        'nssf_no' => $row['nssf_no'] ?? null,
                        'passport_no' => $row['passport_no'] ?? null,
                        'passport_issue_date' => $row['passport_issue_date'] ?? null,
                        'passport_expiry_date' => $row['passport_expiry_date'] ?? null,
                        'address' => $row['address'] ?? null,
                        'permanent_address' => $row['permanent_address'] ?? null,
                        'blood_group' => $row['blood_group'] ?? null,
                    ]);

                    $successful++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            if ($successful === 0 && !empty($errors)) {
                return RequestResponse::badRequest('Failed to import employees.', [
                    'successful' => $successful,
                    'errors' => $errors,
                ]);
            }

            return RequestResponse::ok('Employees imported successfully.', [
                'successful' => $successful,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return RequestResponse::badRequest('Failed to import employees.', ['errors' => [$e->getMessage()]]);
        }
    }

    public function downloadXlsxTemplate()
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $departments = $business->departments()->pluck('name')->toArray();
        $locations = $business->locations()->pluck('name')->toArray();

        $headers = [
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'phone',
            'phone_code',
            'phone_country',
            'password',
            'employee_code',
            'gender',
            'alternate_phone',
            'date_of_birth',
            'marital_status',
            'national_id',
            'place_of_issue',
            'tax_no',
            'nhif_no',
            'nssf_no',
            'passport_no',
            'passport_issue_date',
            'passport_expiry_date',
            'address',
            'permanent_address',
            'blood_group',
            'department',
            'location'
        ];

        $sampleData = [
            [
                'John',
                'Doe',
                'Smith',
                'john.smith@example.com',
                '712345678',
                '254',
                'Kenya',
                'password123',
                'EMP001',
                'male',
                '0712345679',
                '1990-05-15',
                'single',
                '12345678',
                'Nairobi',
                'KRA12345',
                'NHIF56789',
                'NSSF98765',
                'A1234567',
                '2015-06-20',
                '2025-06-20',
                'Olelengpu, Nairobi',
                '456 Avenue, Mombasa',
                'O+',
                $departments[0] ?? '',
                $locations[0] ?? ''
            ]
        ];

        return Excel::download(new class($headers, $sampleData, $departments, $locations) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithValidation,
            \Maatwebsite\Excel\Concerns\WithEvents
        {
            private $headers;
            private $sampleData;
            private $departments;
            private $locations;

            public function __construct(array $headers, array $sampleData, array $departments, array $locations)
            {
                $this->headers = $headers;
                $this->sampleData = $sampleData;
                $this->departments = $departments;
                $this->locations = $locations;
            }

            public function array(): array
            {
                return [$this->headers, ...$this->sampleData];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();

                        // Gender dropdown
                        $validation = $sheet->getCell('J2')->getDataValidation();
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"male,female"');

                        // Department dropdown
                        $validation = $sheet->getCell('Y2')->getDataValidation();
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"' . implode(',', $this->departments) . '"');

                        // Location dropdown
                        $validation = $sheet->getCell('Z2')->getDataValidation();
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setFormula1('"' . implode(',', $this->locations) . '"');
                    }
                ];
            }

            public function rules(): array
            {
                return [
                    'J' => 'in:male,female',
                    'Y' => 'in:' . implode(',', $this->departments),
                    'Z' => 'in:' . implode(',', $this->locations),
                ];
            }
        }, 'employees_template.xlsx');
    }
}