<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Location;
use App\Models\EmployeePaymentDetail;
use App\Models\JobCategory;
use App\Imports\EmployeesImport;
use App\Enums\Status;
use App\Services\NotificationService;
use App\Notifications\SystemAlertNotification;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return redirect()->back()->with('error', 'Business not found.');
        }

        $employees = Employee::where('business_id', $business->id)
            ->with(['user', 'business', 'location', 'department']) // Added 'location' and 'department'
            ->get();
        $departments = Department::where('business_id', $business->id)->get();
        $locations = Location::where('business_id', $business->id)->get();
        $jobCategories = JobCategory::where('business_id', $business->id)->get();

        return view('employees.index', compact('employees', 'departments', 'locations', 'jobCategories', 'business'));
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $query = Employee::where('business_id', $business->id)->with('user', 'department', 'location', 'paymentDetails', 'jobCategory');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }
        if ($department = $request->input('department')) {
            $query->where('department_id', $department);
        }
        if ($location = $request->input('location')) {
            $query->where('location_id', $location);
        }
        if ($jobCategory = $request->input('job_category')) {
            $query->where('id', $jobCategory);
        }

        $employees = $query->paginate(10);
        $data = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->name,
                'employee_code' => $employee->employee_code,
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'location' => $employee->location ? $employee->location->name : $employee->business->company_name,
                'job_category' => $employee->jobCategory ? $employee->jobCategory->name : 'N/A',
                'basic_salary' => number_format((float) ($employee->paymentDetails->basic_salary ?? 0), 2) . ' ' . $employee->paymentDetails->currency  ?? 'N/A',
                'actions' => '<div class="btn-group">' .
                    '<button class="btn btn-sm btn-outline-primary" onclick="viewEmployee(' . $employee->id . ')"><i class="fa fa-eye"></i> View</button>' .
                    '<button class="btn btn-sm btn-outline-warning" onclick="editEmployee(' . $employee->id . ')"><i class="fa fa-edit"></i> Edit</button>' .
                    '<button class="btn btn-sm btn-outline-danger" onclick="deleteEmployee(' . $employee->id . ')"><i class="fa fa-trash"></i> Delete</button>' .
                    '</div>'
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => Employee::where('business_id', $business->id)->count(),
            'recordsFiltered' => $employees->total(),
            'data' => $data->toArray(),
            'message' => 'Employees fetched successfully.',
            'count' => $employees->total(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|string|max:20',
            'employee_code' => 'required|string|unique:employees,employee_code|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'basic_salary' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_mode' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:employee_payment_details,account_number|max:50',
            'bank_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|unique:employees,national_id',
            'tax_no' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'marital_status' => 'required|string|max:20',
            'nhif_no' => 'required|string|max:20',
            'nssf_no' => 'required|string|max:20',
            'permanent_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'profile_picture' => 'nullable|file|image|max:2048',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(16)),
            ]);

            // Create employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'business_id' => $business->id,
                'employee_code' => $validated['employee_code'],
                'department_id' => $validated['department_id'] ?? null,
                'location_id' => $validated['location_id'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'nhif_no' => $validated['nhif_no'] ?? null,
                'nssf_no' => $validated['nssf_no'] ?? null,
                'tax_no' => $validated['tax_no'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            // Create payment details
            $employee->paymentDetails()->create([
                'basic_salary' => $validated['basic_salary'],
                'currency' => $validated['currency'],
                'payment_mode' => $validated['payment_mode'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'bank_name' => $validated['bank_name'],
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $employee->addMedia($request->file('profile_picture'))->toMediaCollection('avatars');
            }

            DB::commit();

            return RequestResponse::created('Employee created successfully.', $employee->id);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($user)) {
                $user->delete();
            }

            return RequestResponse::badRequest('Failed to create employee. ' . $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            $employee = $request->employee_id ? Employee::with('user', 'paymentDetails')->findOrFail($request->employee_id) : null;
            $departments = Department::where('business_id', $business->id)->get();
            $locations = Location::where('business_id', $business->id)->get();

            $form = view('employees._form', compact('employee', 'departments', 'locations', 'business'))->render();
            return response()->json([
                'message' => 'Form loaded successfully.',
                'data' => $form
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error loading employee edit form: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to load form: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'gender' => 'required|string|max:20',
            'employee_code' => 'required|string|unique:employees,employee_code,' . $id . ',id',
            'department_id' => 'nullable|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'basic_salary' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_mode' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:employee_payment_details,account_number,' . $id . ',employee_id',
            'bank_name' => 'required|string|max:255',
            'national_id' => 'required|string|unique:employees,national_id,' . $id . ',id',
            'tax_no' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'marital_status' => 'required|string|max:20',
            'nhif_no' => 'required|string|max:20',
            'nssf_no' => 'required|string|max:20',
            'permanent_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'profile_picture' => 'nullable|file|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($id);

            $employee->update([
                'employee_code' => $validated['employee_code'],
                'department_id' => $validated['department_id'] ?? null,
                'location_id' => $validated['location_id'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
                'tax_no' => $validated['tax_no'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'marital_status' => $validated['marital_status'] ?? null,
                'nhif_no' => $validated['nhif_no'] ?? null,
                'nssf_no' => $validated['nssf_no'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            $employee->user->update([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
            ]);

            $employee->paymentDetails()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'basic_salary' => $validated['basic_salary'],
                    'currency' => $validated['currency'],
                    'payment_mode' => $validated['payment_mode'],
                    'account_name' => $validated['account_name'],
                    'account_number' => $validated['account_number'],
                    'bank_name' => $validated['bank_name'],
                ]
            );

            if ($request->hasFile('profile_picture')) {
                $employee->clearMediaCollection('avatars');
                $employee->addMedia($request->file('profile_picture'))->toMediaCollection('avatars');
            }

            DB::commit();
            return RequestResponse::ok('Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return RequestResponse::badRequest('Failed to update employee: ' . $e->getMessage());
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            DB::table('leave_entitlements')->where('employee_id', $employee->id)->delete();

            $employee->clearMediaCollection('avatars');
            $employee->paymentDetails()->delete();
            $employee->user->delete();
            $employee->delete();

            return RequestResponse::ok('Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to delete employee: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        try {
            $employee = Employee::with([
                'user',
                'business',
                'department',
                'location',
                'paymentDetails',
                'payrolls.payroll',
                'employeeAllowances.allowance',
                'employeeDeductions.deduction',
                'attendances',
                'leaveRequests',
                'loans',
                'overtimes',
                'familyMembers',
                'emergencyContacts',
                'documents',
                'advances',
                'academicDetails',
                'previousEmployment',
                'employmentDetails',
                'jobCategory',
                'spouse',
            ])->findOrFail($request->employee_id);

            $view = view('employees._view', compact('employee'))->render();
            return response()->json([
                'message' => 'Employee details loaded successfully.',
                'data' => $view
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error loading employee view: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to load employee details: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
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
            'jobCategories' => 'array|nullable',
            'jobCategories.*' => [
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

            if (!empty($validatedData['jobCategories']) && !in_array('all', $validatedData['jobCategories'])) {
                $jobCategoryIds = JobCategory::whereIn('slug', $validatedData['jobCategories'])->pluck('id');
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
                    $user->setStatus('active');
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
