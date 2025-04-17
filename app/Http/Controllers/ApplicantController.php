<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Applicant;
use App\Models\JobPost;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicantsExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicantStatusUpdated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApplicantController extends Controller
{
    use HandleTransactions;

    public function index()
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $jobPosts = JobPost::where('business_id', $business->id)->get();
        return view('applicants.index', ['page' => 'Job Applicants', 'jobPosts' => $jobPosts]);
    }

    public function fetch(Request $request)
    {
        try {
            $role = Role::findByName('applicant');
            if (!$role) {
                return RequestResponse::badRequest('Applicant role not found.');
            }

            $business = Business::findBySlug(session('active_business_slug'));
            $query = User::role($role->name)
                ->with(['applicant', 'applicant.applications.jobPost'])
                ->whereHas('applicant', function ($q) use ($business) {
                    $q->where(function ($subQ) use ($business) {
                        $subQ->whereIn('created_by', function ($employeeQ) use ($business) {
                            $employeeQ->select('user_id')
                                ->from('employees')
                                ->where('business_id', $business->id);
                        })
                            ->orWhere('created_by', $business->user_id);
                    });
                });

            $this->applyFilters($query, $request);

            $applicants = $query->paginate(10);
            $applicant_table = view('applicants._table', compact('applicants'))->render();
            return RequestResponse::ok('Ok', $applicant_table);
        } catch (\Exception $e) {
            Log::error('Error fetching applicants: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return RequestResponse::badRequest('Failed to fetch applicants: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        try {
            $role = Role::findByName('applicant');
            if (!$role) {
                return RequestResponse::badRequest('Applicant role not found.');
            }

            $business = Business::findBySlug(session('active_business_slug'));
            $query = User::role($role->name)
                ->with(['applicant', 'applicant.applications.jobPost'])
                ->whereHas('applicant', function ($q) use ($business) {
                    $q->where(function ($subQ) use ($business) {
                        $subQ->whereIn('created_by', function ($employeeQ) use ($business) {
                            $employeeQ->select('user_id')
                                ->from('employees')
                                ->where('business_id', $business->id);
                        })
                            ->orWhere('created_by', $business->user_id);
                    });
                });

            $this->applyFilters($query, $request);

            $applicants = $query->paginate(10);
            $applicant_table = view('applicants._table', compact('applicants'))->render();
            return RequestResponse::ok('Ok', $applicant_table);
        } catch (\Exception $e) {
            Log::error('Error filtering applicants: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return RequestResponse::badRequest('Failed to filter applicants: ' . $e->getMessage());
        }
    }

    private function applyFilters($query, $request)
    {
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', "%$filter%")
                    ->orWhere('email', 'like', "%$filter%")
                    ->orWhereHas('applicant', function ($q) use ($filter) {
                        $q->where('city', 'like', "%$filter%")
                            ->orWhere('country', 'like', "%$filter%")
                            ->orWhere('experience_level', 'like', "%$filter%");
                    });
            });
        }

        if ($request->has('job_post_id')) {
            $query->whereHas('applicant.applications', function ($q) use ($request) {
                $q->where('job_post_id', $request->job_post_id);
            });
        }

        if ($request->has('location')) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('city', 'like', "%{$request->location}%")
                    ->orWhere('country', 'like', "%{$request->location}%");
            });
        }
    }

    public function create()
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $users = User::whereDoesntHave('applicant')->get();
        $jobPosts = JobPost::where('business_id', $business->id)->get();
        $applicant = null;
        return view('applicants.create', compact('users', 'jobPosts', 'applicant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'linkedin_profile' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'current_job_title' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|in:Entry-level,Mid-level,Senior',
            'education_level' => 'nullable|string|in:High School,Bachelor\'s,Master\'s,PhD',
            'desired_salary' => 'nullable|numeric',
            'job_preferences' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $business = Business::findBySlug(session('active_business_slug'));

            $countryCode = $request->code;
            $phoneNumber = "+{$countryCode}{$request->phone}";

            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            $name = trim("{$request->first_name} {$request->middle_name} {$request->last_name}");
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'phone' => $phoneNumber,
                'password' => Hash::make($request->password),
                'country' => $request->country,
            ]);
            $user->assignRole('applicant');
            $user->setStatus(Status::ACTIVE);

            $request->hasFile('image')
                ? $user->addMediaFromRequest('image')->toMediaCollection('avatars')
                : $user->addMediaFromBase64(createAvatarImageFromName($name))->toMediaCollection('avatars');

            $applicant = Applicant::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'linkedin_profile' => $request->linkedin_profile,
                'portfolio_url' => $request->portfolio_url,
                'current_job_title' => $request->current_job_title,
                'current_company' => $request->current_company,
                'experience_level' => $request->experience_level,
                'education_level' => $request->education_level,
                'desired_salary' => $request->desired_salary,
                'job_preferences' => $request->job_preferences,
                'source' => $request->source,
                'created_by' => auth()->id(),
            ]);

            $applicant->setStatus(Status::ACTIVE);
            return RequestResponse::created('Applicant created successfully');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $applicant = Applicant::with('user')
            ->where('id', $validatedData['applicant_id'])
            ->where(function ($q) use ($business) {
                $q->whereIn('created_by', function ($subQ) use ($business) {
                    $subQ->select('user_id')
                        ->from('employees')
                        ->where('business_id', $business->id);
                })
                    ->orWhere('created_by', $business->user_id);
            })
            ->firstOrFail();

        $users = User::whereDoesntHave('applicant')->orWhere('id', $applicant->user_id)->get();
        $jobPosts = JobPost::where('business_id', $business->id)->get();
        $applicant_form = view('applicants._form', compact('applicant', 'users', 'jobPosts'))->render();
        return RequestResponse::ok('Ok', $applicant_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . ($request->input('applicant_id') ? Applicant::findOrFail($request->input('applicant_id'))->user->id : null),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'linkedin_profile' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'summary' => 'nullable|string',
            'current_job_title' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|in:Entry-level,Mid-level,Senior',
            'education_level' => 'nullable|string|in:High School,Bachelor\'s,Master\'s,PhD',
            'desired_salary' => 'nullable|string',
            'job_preferences' => 'nullable|string',
            'source' => 'nullable|string|max:255',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $applicant = Applicant::where('id', $validatedData['applicant_id'])
                ->where(function ($q) use ($business) {
                    $q->whereIn('created_by', function ($subQ) use ($business) {
                        $subQ->select('user_id')
                            ->from('employees')
                            ->where('business_id', $business->id);
                    })
                        ->orWhere('created_by', $business->user_id);
                })
                ->firstOrFail();

            $user = $applicant->user;

            if (isset($validatedData['first_name']) || isset($validatedData['middle_name']) || isset($validatedData['last_name'])) {
                $nameParts = array_filter([
                    $validatedData['first_name'] ?? '',
                    $validatedData['middle_name'] ?? '',
                    $validatedData['last_name'] ?? ''
                ]);
                $user->name = !empty($nameParts) ? trim(implode(' ', $nameParts)) : $user->name;
            }

            if (isset($validatedData['email'])) {
                $user->email = $validatedData['email'];
            }

            if (isset($validatedData['phone'])) {
                $user->phone = $validatedData['phone'];
            }

            $user->save();

            $applicant->update(array_filter([
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'zip_code' => $validatedData['zip_code'],
                'country' => $validatedData['country'],
                'linkedin_profile' => $validatedData['linkedin_profile'],
                'portfolio_url' => $validatedData['portfolio_url'],
                'current_job_title' => $validatedData['current_job_title'],
                'current_company' => $validatedData['current_company'],
                'experience_level' => $validatedData['experience_level'],
                'education_level' => $validatedData['education_level'],
                'desired_salary' => $validatedData['desired_salary'],
                'job_preferences' => $validatedData['job_preferences'],
                'source' => $validatedData['source'],
            ]));

            $applicant->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Applicant updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_ids' => 'required|array',
            'applicant_ids.*' => 'exists:applicants,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $applicants = Applicant::with('applications', 'skills', 'user')
                ->whereIn('id', $validatedData['applicant_ids'])
                ->where(function ($q) use ($business) {
                    $q->whereIn('created_by', function ($subQ) use ($business) {
                        $subQ->select('user_id')
                            ->from('employees')
                            ->where('business_id', $business->id);
                    })
                        ->orWhere('created_by', $business->user_id);
                })
                ->get();

            if ($applicants->isEmpty()) {
                return RequestResponse::badRequest('No applicants found for this business.');
            }

            foreach ($applicants as $applicant) {
                $applicant->applications()->delete();
                $applicant->skills()->detach();
                $applicant->delete();
                if ($applicant->user) {
                    $applicant->user->delete();
                }
            }

            return RequestResponse::ok('Selected applicants deleted successfully.');
        });
    }

    public function view($business, Applicant $applicant)
    {
        $businessModel = Business::findBySlug($business);
        $applicantQuery = Applicant::with(['applications.jobPost'])
            ->where('id', $applicant->id)
            ->where(function ($q) use ($businessModel) {
                $q->whereIn('created_by', function ($subQ) use ($businessModel) {
                    $subQ->select('user_id')
                        ->from('employees')
                        ->where('business_id', $businessModel->id);
                })
                    ->orWhere('created_by', $businessModel->user_id);
            })
            ->firstOrFail();

        $applications = $applicantQuery->applications()
            ->with('jobPost')
            ->where('business_id', $businessModel->id)
            ->get();

        return view('applicants._view', compact('applicant', 'applications'));
    }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $applicant = Applicant::with(['user', 'applications.jobPost'])
            ->where('id', $validatedData['applicant_id'])
            ->where(function ($q) use ($business) {
                $q->whereIn('created_by', function ($subQ) use ($business) {
                    $subQ->select('user_id')
                        ->from('employees')
                        ->where('business_id', $business->id);
                })
                    ->orWhere('created_by', $business->user_id);
            })
            ->firstOrFail();

        return RequestResponse::ok('Ok', $applicant->toArray());
    }

    public function downloadDocument(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'media_id' => 'required|exists:media,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $applicant = Applicant::where('id', $validatedData['applicant_id'])
            ->where(function ($q) use ($business) {
                $q->whereIn('created_by', function ($subQ) use ($business) {
                    $subQ->select('user_id')
                        ->from('employees')
                        ->where('business_id', $business->id);
                })
                    ->orWhere('created_by', $business->user_id);
            })
            ->firstOrFail();

        $media = $applicant->applications
            ->flatMap->getMedia('applications')
            ->firstWhere('id', $validatedData['media_id']);

        if ($media) {
            $fileStream = response()->streamDownload(function () use ($media) {
                echo file_get_contents($media->getPath());
            }, $media->file_name, ['Content-Type' => $media->mime_type]);
            $fileStream->headers->set('X-Filename', $media->file_name);
            return $fileStream;
        }
        return RequestResponse::badRequest('Document not found.');
    }

    public function export(Request $request)
    {
        $role = Role::findByName('applicant');
        if (!$role) {
            return RequestResponse::badRequest('Applicant role not found.');
        }

        $business = Business::findBySlug(session('active_business_slug'));
        $query = User::role($role->name)
            ->with('applicant')
            ->whereHas('applicant', function ($q) use ($business) {
                $q->where(function ($subQ) use ($business) {
                    $subQ->whereIn('created_by', function ($employeeQ) use ($business) {
                        $employeeQ->select('user_id')
                            ->from('employees')
                            ->where('business_id', $business->id);
                    })
                        ->orWhere('created_by', $business->user_id);
                });
            });

        $this->applyFilters($query, $request);

        return Excel::download(new ApplicantsExport($query->get()), 'applicants_' . now()->format('Ymd_His') . '.xlsx');
    }
}
