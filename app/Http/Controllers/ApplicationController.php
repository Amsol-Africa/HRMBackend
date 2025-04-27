<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\JobPost;
use App\Models\Business;
use App\Models\Application;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\ApplicationReceived;
use App\Mail\ApplicationStageUpdated;
use App\Mail\InterviewScheduled;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicationExport;

class ApplicationController extends Controller
{
    use HandleTransactions;

    public function index()
    {
        $page = 'Job Applications';
        $jobPosts = JobPost::all();
        return view('applications.index', compact('page', 'jobPosts'));
    }

    public function create()
    {
        $applicants = Applicant::with('user')->get();
        $job_posts = JobPost::all();
        return view('applications.create', compact('applicants', 'job_posts'));
    }

    public function view($business, Application $application)
    {
        $application->load('applicant.user', 'jobPost', 'interviews', 'createdBy');
        return view('applications.view', compact('application'));
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $query = $business->applications()->with(['applicant.user', 'jobPost', 'createdBy', 'interviews']);

        if ($request->has('filter') && !empty($request->input('filter'))) {
            $filter = $request->input('filter');
            $query->where(function ($q) use ($filter) {
                $q->whereHas('applicant.user', function ($q) use ($filter) {
                    $q->where('name', 'like', "%$filter%")
                        ->orWhere('email', 'like', "%$filter%");
                })->orWhereHas('jobPost', function ($q) use ($filter) {
                    $q->where('title', 'like', "%$filter%");
                })->orWhere('stage', 'like', "%$filter%");
            });
        }

        if ($request->has('job_post_id') && !empty($request->input('job_post_id'))) {
            $query->where('job_post_id', $request->job_post_id);
        }

        if ($request->has('location') && !empty($request->input('location'))) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->location}%");
            });
        }

        $applications = $query->paginate(10);
        $view = view('applications._table', compact('applications'))->render();
        return RequestResponse::ok('Ok', $view);
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with('applicant.user', 'jobPost')->findOrFail($validatedData['application_id']);
        $applicants = Applicant::with('user')->get();
        $job_posts = JobPost::all();
        $application_form = view('applications._form', compact('application', 'applicants', 'job_posts'))->render();
        return RequestResponse::ok('Ok', $application_form);
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => [
                'required',
                'exists:applicants,id',
                function ($attribute, $value, $fail) use ($request) {
                    $job_post = JobPost::findBySlug($request->job_post_id);
                    if (Application::where('applicant_id', $value)
                        ->where('job_post_id', $job_post->id)
                        ->exists()
                    ) {
                        $fail('You have already applied to this job.');
                    }
                },
            ],
            'job_post_id' => 'required|exists:job_posts,slug',
            'cover_letter' => 'nullable|string',
            'attachments.*' => 'file|mimes:pdf,doc,docx|max:2048',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $job_post = JobPost::findBySlug($request->job_post_id);
            $business = $job_post->business;
            $location = $job_post->location;

            $application = Application::create([
                'business_id' => $business?->id,
                'location_id' => $location?->id,
                'applicant_id' => $request->applicant_id,
                'job_post_id' => $job_post->id,
                'cover_letter' => $request->cover_letter,
                'stage' => 'applied',
                'created_by' => Auth::id(),
            ]);

            $application->setStatus(Status::APPLIED);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $application->addMedia($file)->toMediaCollection('applications');
                }
            }

            Mail::to($application->applicant->user->email)->send(new ApplicationReceived($application));

            return RequestResponse::created('Application submitted successfully');
        });
    }

    public function externalStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'api_token' => 'required|string',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/|unique:users,phone',
                'job_post_id' => [
                    'required',
                    'exists:job_posts,slug',
                    function ($attribute, $value, $fail) use ($request) {
                        $job_post = JobPost::where('slug', $value)
                            ->where('is_public', true)
                            ->where('status', 'open')
                            ->first();
                        if ($job_post && $request->email) {
                            $user = User::where('email', $request->email)->first();
                            $applicant = $user ? Applicant::where('user_id', $user->id)->first() : null;
                            if (
                                $applicant && Application::where('applicant_id', $applicant->id)
                                ->where('job_post_id', $job_post->id)
                                ->exists()
                            ) {
                                $fail('You have already applied to this job.');
                            }
                        }
                    },
                ],
                'cover_letter' => 'nullable|string|max:5000',
                'attachments.*' => 'file|mimes:pdf,doc,docx|max:2048',
                'attachments' => 'array|max:5',
            ], [
                'email.unique' => 'This email is already registered.',
                'phone.unique' => 'This phone number is already registered.',
                'job_post_id.exists' => 'The specified job post does not exist.',
                'attachments.*.mimes' => 'Only PDF, DOC, or DOCX files are allowed.',
            ]);

            $business = Business::where('slug', 'amsol')->first();

            if (!$business) {
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            if (!$business->api_token) {
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            try {
                if (!Hash::check($validated['api_token'], $business->api_token)) {
                    return RequestResponse::unauthorized('Invalid or unauthorized API token.');
                }
            } catch (\Exception $e) {
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            $jobPost = JobPost::where('slug', $validated['job_post_id'])
                ->where('business_id', $business->id)
                ->where('is_public', true)
                ->where('status', 'open')
                ->first();

            if (!$jobPost) {
                return RequestResponse::badRequest('Invalid or unavailable job post.');
            }

            if ($jobPost->closing_date && now()->gt($jobPost->closing_date)) {
                return RequestResponse::badRequest('This job post is closed.');
            }

            return $this->handleTransaction(function () use ($validated, $business, $jobPost, $request) {
                $user = User::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                        'phone' => $validated['phone'],
                        'password' => Hash::make(Str::random(12)),
                        'country' => 'Unknown',
                    ]
                );
                $user->assignRole('applicant');
                $user->setStatus(Status::ACTIVE);

                $applicant = Applicant::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'address' => 'N/A',
                        'country' => 'Unknown',
                        'created_by' => null,
                    ]
                );

                $application = Application::create([
                    'business_id' => $business->id,
                    'location_id' => $jobPost->location_id ?? null,
                    'applicant_id' => $applicant->id,
                    'job_post_id' => $jobPost->id,
                    'cover_letter' => $validated['cover_letter'],
                    'stage' => 'applied',
                    'created_by' => null,
                ]);
                $application->setStatus(Status::APPLIED);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $application->addMedia($file)->toMediaCollection('applications');
                    }
                }

                Mail::to($user->email)->queue(new ApplicationReceived($application));

                return RequestResponse::ok('Application submitted successfully', [
                    'application_id' => $application->id,
                    'job_title' => $jobPost->title,
                    'status' => $application->stage,
                ]);
            }, function ($exception) {
                return RequestResponse::badRequest('An error occurred while submitting your application: ' . $exception->getMessage());
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return RequestResponse::badRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return RequestResponse::badRequest('An unexpected error occurred.');
        }
    }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::with('applicant.user', 'jobPost', 'interviews')->findOrFail($validatedData['application_id']);
        return RequestResponse::ok('Ok', $application->toArray());
    }

    public function reports()
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $applications = $business->applications()
            ->with('applicant.user', 'jobPost')
            ->latest()
            ->get();

        return view('applications.reports', compact('applications'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'cover_letter' => 'nullable|string',
            'stage' => 'required|string|in:applied,shortlisted,in_progress,rejected,finished',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $application = Application::findOrFail($request->application_id);
            $oldStage = $application->stage;
            $application->update($request->only(['cover_letter', 'stage']));

            if ($oldStage !== $request->stage) {
                Mail::to($application->applicant->user->email)->send(new ApplicationStageUpdated($application));
            }

            return RequestResponse::ok('Application updated successfully', $application);
        });
    }

    public function updateStage(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'stage' => 'required|string|in:applied,shortlisted,in_progress,rejected,finished',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $applications = Application::whereIn('id', $request->application_ids)->get();
            foreach ($applications as $application) {
                $oldStage = $application->stage;
                $application->update(['stage' => $request->stage]);
                if ($oldStage !== $request->stage) {
                    Mail::to($application->applicant->user->email)->send(new ApplicationStageUpdated($application));
                }
            }
            return RequestResponse::ok("Stage updated to {$request->stage} for selected applications and emails sent.");
        });
    }

    public function shortlist(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $applications = Application::whereIn('id', $request->application_ids)->get();
            foreach ($applications as $application) {
                $application->update(['stage' => 'shortlisted']);
                Mail::to($application->applicant->user->email)->send(new ApplicationStageUpdated($application));
            }
            return RequestResponse::ok('Selected applications shortlisted successfully.');
        });
    }

    public function scheduleInterview(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'interview_date' => 'required|date',
            'interview_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'interviewer_id' => 'required|exists:users,id',
            'type' => 'required|in:phone,video,in-person',
            'meeting_link' => 'nullable|url',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $application = Application::findOrFail($request->application_id);
            $scheduledAt = \Carbon\Carbon::parse("{$request->interview_date} {$request->interview_time}")->toDateTimeString();

            $interview = Interview::create([
                'application_id' => $application->id,
                'scheduled_at' => $scheduledAt,
                'location' => $request->location,
                'interviewer_id' => $request->interviewer_id,
                'type' => $request->type,
                'meeting_link' => $request->meeting_link,
                'status' => 'scheduled',
                'created_by' => Auth::id(),
            ]);

            $application->update(['stage' => 'in_progress']);
            Mail::to($application->applicant->user->email)->send(new InterviewScheduled($application, $interview));
            return RequestResponse::ok('Interview scheduled and email sent.');
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
        ]);

        return $this->handleTransaction(function () use ($request) {
            Application::whereIn('id', $request->application_ids)->delete();
            return RequestResponse::ok('Selected applications deleted successfully');
        });
    }

    public function export(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $query = $business->applications()->with('applicant.user', 'jobPost');
        $applications = $query->get();

        if ($applications->isEmpty()) {
            return response()->json(['message' => 'No applications available to export'], 400);
        }

        return Excel::download(new ApplicationExport($applications), 'applications_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}