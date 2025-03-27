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
            ->with([
                'user',
                'business',
                'location',
                'department',
                'employmentDetails.jobCategory',
                'paymentDetails'
            ])
            ->get();

        $departments = Department::where('business_id', $business->id)->get();
        $locations = Location::where('business_id', $business->id)->get();
        $jobCategories = JobCategory::where('business_id', $business->id)->get();

        return view('employees.index', compact('employees', 'departments', 'locations', 'jobCategories', 'business'));
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $query = Employee::where('business_id', $business->id)
            ->with(['user', 'department', 'location', 'paymentDetails', 'employmentDetails.jobCategory']);

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
            $query->whereHas('employmentDetails', function ($q) use ($jobCategory) {
                $q->where('job_category_id', $jobCategory);
            });
        }

        $employees = $query->paginate(10);
        $data = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user->name,
                'employee_code' => $employee->employee_code,
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'job_category' => optional($employee->employmentDetails)->jobCategory ? $employee->employmentDetails->jobCategory->name : 'N/A',
                'location' => $employee->location ? $employee->location->name : $employee->business->company_name,
                'basic_salary' => number_format((float) (optional($employee->paymentDetails)->basic_salary ?? 0), 2) . ' ' . (optional($employee->paymentDetails)->currency ?? 'N/A'),
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
            'job_category_id' => 'nullable|exists:job_categories,id',
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
            'employment_term' => 'required|in:permanent,contract,temporary,internship',
        ]);

        Log::debug('Creating Employee - Validated Data:', $validated);

        $business = Business::findBySlug(session('active_business_slug'));

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => "amsol_employee",
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'business_id' => $business->id,
                'employee_code' => $validated['employee_code'],
                'department_id' => $validated['department_id'] ?? null,
                'job_category_id' => $validated['job_category_id'] ?? null,
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

            $employee->employmentDetails()->create([
                'job_category_id' => $validated['job_category_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'employment_date' => $validated['employment_date'] ?? now(),
                'employment_term' => $validated['employment_term'],
            ]);

            $employee->paymentDetails()->create([
                'basic_salary' => $validated['basic_salary'],
                'currency' => $validated['currency'],
                'payment_mode' => $validated['payment_mode'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'bank_name' => $validated['bank_name'],
            ]);

            DB::commit();
            Log::debug('Employee created successfully.', ['employee_id' => $employee->id]);

            return RequestResponse::created('Employee created successfully.', $employee->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee.', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to create employee. ' . $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            $employee = $request->employee_id ? Employee::with('user', 'paymentDetails', 'employmentDetails')->findOrFail($request->employee_id) : null;
            $departments = Department::where('business_id', $business->id)->get();
            $locations = Location::where('business_id', $business->id)->get();
            $jobCategories = JobCategory::where('business_id', $business->id)->get();

            $form = view('employees._form', compact('employee', 'departments', 'locations', 'jobCategories', 'business'))->render();
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
            'job_category_id' => 'nullable|exists:job_categories,id',
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
            'employment_term' => 'required|in:permanent,contract,temporary,internship',
        ]);

        Log::info('Updating employee ID: ' . $id, ['validated_data' => $validated]);

        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($id);

            Log::info('Before Update - Employee Data', ['employee' => $employee->toArray()]);

            $employee->update([
                'employee_code' => $validated['employee_code'],
                'department_id' => $validated['department_id'] ?? null,
                'job_category_id' => $validated['job_category_id'] ?? null,
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

            Log::info('After Update - Employee Data', ['employee' => $employee->toArray()]);

            $employee->user->update([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            $employmentDetails = $employee->employmentDetails()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'job_category_id' => $validated['job_category_id'] ?? null,
                    'department_id' => $validated['department_id'] ?? null,
                    'employment_term' => $validated['employment_term'],
                    'employment_date' => $validated['employment_date'] ?? now(),
                ]
            );

            Log::info('Employment Details Updated', ['employment_details' => $employmentDetails->toArray()]);

            $paymentDetails = $employee->paymentDetails()->updateOrCreate(
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

            Log::info('Payment Details Updated', ['payment_details' => $paymentDetails->toArray()]);

            if ($request->hasFile('profile_picture')) {
                $employee->clearMediaCollection('avatars');
                $employee->addMedia($request->file('profile_picture'))->toMediaCollection('avatars');
                Log::info('Profile picture updated for employee ID: ' . $id);
            }

            DB::commit();
            Log::info('Employee update transaction committed.', ['employee_id' => $id]);

            return RequestResponse::ok('Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee: ' . $e->getMessage(), [
                'employee_id' => $id,
                'request_data' => $request->all()
            ]);

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
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $errors = [];
        $successful = 0;

        $departments = $business->departments()->pluck('id', 'name')->toArray();
        $locations = $business->locations()->pluck('id', 'name')->toArray();
        $locations[$business->name] = null; // Add business name as a valid location (no ID needed if not in DB)
        $jobCategories = $business->job_categories()->pluck('id', 'name')->toArray();

        try {
            DB::beginTransaction();

            $records = Excel::toArray(new EmployeesImport, $file)[0];
            if (empty($records)) {
                return RequestResponse::badRequest('The uploaded XLSX file is empty or invalid.', ['errors' => ['No data found in the file.']]);
            }
            $headers = array_shift($records);
            $records = array_map(function ($row) use ($headers) {
                return array_combine($headers, array_map('trim', $row));
            }, $records);

            foreach ($records as $index => $row) {
                try {
                    $validator = Validator::make($row, [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email|unique:users,email',
                        'gender' => 'required|string|max:20',
                        'employee_code' => 'required|string|unique:employees,employee_code|max:50',
                        'department' => 'nullable|string|exists:departments,name',
                        'job_category' => 'nullable|string|exists:job_categories,name',
                        'location' => 'nullable|string|in:' . implode(',', array_keys($locations)), // Updated to include business name
                        'basic_salary' => 'required|numeric|min:0',
                        'currency' => 'required|string|size:3',
                        'payment_mode' => 'required|in:bank,cash,cheque,mpesa', // Updated to match enum
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
                        'employment_term' => 'required|in:permanent,contract,temporary,internship',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    $departmentId = !empty($row['department']) ? ($departments[$row['department']] ?? null) : null;
                    $jobCategoryId = !empty($row['job_category']) ? ($jobCategories[$row['job_category']] ?? null) : null;
                    $locationId = !empty($row['location']) && isset($locations[$row['location']]) ? $locations[$row['location']] : null;

                    if (!empty($row['department']) && !$departmentId) {
                        $errors[] = "Row " . ($index + 2) . ": Department '{$row['department']}' not found.";
                        continue;
                    }
                    if (!empty($row['job_category']) && !$jobCategoryId) {
                        $errors[] = "Row " . ($index + 2) . ": Job Category '{$row['job_category']}' not found.";
                        continue;
                    }
                    if (!empty($row['location']) && !array_key_exists($row['location'], $locations)) {
                        $errors[] = "Row " . ($index + 2) . ": Location '{$row['location']}' not found.";
                        continue;
                    }

                    $user = User::create([
                        'name' => trim("{$row['first_name']} {$row['last_name']}"),
                        'email' => $row['email'],
                        'phone' => $row['phone'],
                        'password' => Hash::make("amsol_employee"),
                    ]);

                    $employee = $business->employees()->create([
                        'user_id' => $user->id,
                        'employee_code' => $row['employee_code'],
                        'department_id' => $departmentId,
                        'job_category_id' => $jobCategoryId,
                        'location_id' => $locationId,
                        'national_id' => $row['national_id'] ?? null,
                        'marital_status' => $row['marital_status'],
                        'nhif_no' => $row['nhif_no'],
                        'nssf_no' => $row['nssf_no'],
                        'tax_no' => $row['tax_no'] ?? null,
                        'date_of_birth' => $row['date_of_birth'],
                        'gender' => $row['gender'],
                        'phone' => $row['phone'],
                        'permanent_address' => $row['permanent_address'],
                    ]);

                    $employee->employmentDetails()->create([
                        'job_category_id' => $jobCategoryId,
                        'department_id' => $departmentId,
                        'employment_date' => now(),
                        'employment_term' => $row['employment_term'],
                    ]);

                    $employee->paymentDetails()->create([
                        'basic_salary' => $row['basic_salary'],
                        'currency' => $row['currency'],
                        'payment_mode' => $row['payment_mode'], // Now matches enum
                        'account_name' => $row['account_name'],
                        'account_number' => $row['account_number'],
                        'bank_name' => $row['bank_name'],
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
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $departments = $business->departments()->pluck('name')->toArray();
        $locations = $business->locations()->pluck('name')->toArray();
        $jobCategories = $business->job_categories()->pluck('name')->toArray();

        // Add the main business name to locations
        $locations[] = $business->name;

        $headers = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'employee_code',
            'department',
            'job_category',
            'location',
            'basic_salary',
            'currency',
            'payment_mode',
            'account_name',
            'account_number',
            'bank_name',
            'national_id',
            'tax_no',
            'date_of_birth',
            'marital_status',
            'nhif_no',
            'nssf_no',
            'permanent_address',
            'employment_term',
        ];

        $sampleData = [
            [
                'John',
                'Smith',
                'john.smith@example.com',
                '+254712345678',
                'male',
                'EMP001',
                $departments[0] ?? 'HR',
                $jobCategories[0] ?? 'Manager',
                $locations[0] ?? 'Nairobi',
                '50000.00',
                'KES',
                'bank', // Updated from 'bank_transfer' to match enum
                'John Smith',
                '1234567890',
                'Equity Bank',
                '12345678',
                'KRA12345',
                '1990-05-15',
                'single',
                'NHIF56789',
                'NSSF98765',
                'P.O. Box 456, Nairobi',
                'permanent',
            ]
        ];

        return Excel::download(new class($headers, $sampleData, $departments, $locations, $jobCategories) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithValidation,
            \Maatwebsite\Excel\Concerns\WithEvents
        {
            private $headers;
            private $sampleData;
            private $departments;
            private $locations;
            private $jobCategories;

            public function __construct(array $headers, array $sampleData, array $departments, array $locations, array $jobCategories)
            {
                $this->headers = $headers;
                $this->sampleData = $sampleData;
                $this->departments = $departments;
                $this->locations = $locations;
                $this->jobCategories = $jobCategories;
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

                        // Style headers: bold and background color
                        $sheet->getStyle('A1:W1')->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFCCCCCC'],
                            ],
                        ]);

                        // Set column widths for readability
                        $sheet->getColumnDimension('A')->setWidth(15);
                        $sheet->getColumnDimension('B')->setWidth(15);
                        $sheet->getColumnDimension('C')->setWidth(25);
                        $sheet->getColumnDimension('D')->setWidth(15);
                        $sheet->getColumnDimension('E')->setWidth(10);
                        $sheet->getColumnDimension('F')->setWidth(12);
                        $sheet->getColumnDimension('G')->setWidth(15);
                        $sheet->getColumnDimension('H')->setWidth(15);
                        $sheet->getColumnDimension('I')->setWidth(15);
                        $sheet->getColumnDimension('J')->setWidth(12);
                        $sheet->getColumnDimension('K')->setWidth(10);
                        $sheet->getColumnDimension('L')->setWidth(15);
                        $sheet->getColumnDimension('M')->setWidth(20);
                        $sheet->getColumnDimension('N')->setWidth(15);
                        $sheet->getColumnDimension('O')->setWidth(15);
                        $sheet->getColumnDimension('P')->setWidth(12);
                        $sheet->getColumnDimension('Q')->setWidth(12);
                        $sheet->getColumnDimension('R')->setWidth(12);
                        $sheet->getColumnDimension('S')->setWidth(12);
                        $sheet->getColumnDimension('T')->setWidth(12);
                        $sheet->getColumnDimension('U')->setWidth(12);
                        $sheet->getColumnDimension('V')->setWidth(25);
                        $sheet->getColumnDimension('W')->setWidth(15);

                        // Define helper function to apply validation to a range
                        $applyValidation = function ($range, $options) use ($sheet) {
                            $validation = $sheet->getDataValidation($range);
                            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                            $validation->setAllowBlank($options['allowBlank'] ?? false);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setPromptTitle($options['promptTitle']);
                            $validation->setPrompt($options['prompt']);
                            $validation->setErrorTitle($options['errorTitle']);
                            $validation->setError($options['error']);
                            $validation->setFormula1($options['formula']);
                        };

                        // Apply validations to ranges (rows 2 to 1000)
                        $applyValidation('E2:E1000', [
                            'promptTitle' => 'Gender',
                            'prompt' => 'Select gender',
                            'errorTitle' => 'Invalid Gender',
                            'error' => 'Please select a valid gender from the list.',
                            'formula' => '"male,female"',
                            'allowBlank' => false,
                        ]);

                        $applyValidation('G2:G1000', [
                            'promptTitle' => 'Department',
                            'prompt' => 'Select a department (optional)',
                            'errorTitle' => 'Invalid Department',
                            'error' => 'Please select a valid department from the list.',
                            'formula' => '"' . implode(',', $this->departments) . '"',
                            'allowBlank' => true,
                        ]);

                        $applyValidation('H2:H1000', [
                            'promptTitle' => 'Job Category',
                            'prompt' => 'Select a job category (optional)',
                            'errorTitle' => 'Invalid Job Category',
                            'error' => 'Please select a valid job category from the list.',
                            'formula' => '"' . implode(',', $this->jobCategories) . '"',
                            'allowBlank' => true,
                        ]);

                        $applyValidation('I2:I1000', [
                            'promptTitle' => 'Location',
                            'prompt' => 'Select a location (optional)',
                            'errorTitle' => 'Invalid Location',
                            'error' => 'Please select a valid location from the list.',
                            'formula' => '"' . implode(',', $this->locations) . '"',
                            'allowBlank' => true,
                        ]);

                        $applyValidation('L2:L1000', [
                            'promptTitle' => 'Payment Mode',
                            'prompt' => 'Select a payment mode',
                            'errorTitle' => 'Invalid Payment Mode',
                            'error' => 'Please select a valid payment mode from the list.',
                            'formula' => '"bank,cash,cheque,mpesa"', // Updated to match enum
                            'allowBlank' => false,
                        ]);

                        $applyValidation('S2:S1000', [
                            'promptTitle' => 'Marital Status',
                            'prompt' => 'Select marital status',
                            'errorTitle' => 'Invalid Marital Status',
                            'error' => 'Please select a valid marital status from the list.',
                            'formula' => '"single,married,divorced,widowed"',
                            'allowBlank' => false,
                        ]);

                        $applyValidation('W2:W1000', [
                            'promptTitle' => 'Employment Term',
                            'prompt' => 'Select employment term',
                            'errorTitle' => 'Invalid Employment Term',
                            'error' => 'Please select a valid employment term from the list.',
                            'formula' => '"permanent,contract,temporary,internship"',
                            'allowBlank' => false,
                        ]);
                    }
                ];
            }

            public function rules(): array
            {
                return [
                    'E' => 'in:male,female',
                    'G' => 'nullable|in:' . implode(',', $this->departments),
                    'H' => 'nullable|in:' . implode(',', $this->jobCategories),
                    'I' => 'nullable|in:' . implode(',', $this->locations),
                    'L' => 'in:bank,cash,cheque,mpesa', // Updated to match enum
                    'S' => 'in:single,married,divorced,widowed',
                    'W' => 'in:permanent,contract,temporary,internship',
                ];
            }
        }, 'employees_template.xlsx');
    }
}