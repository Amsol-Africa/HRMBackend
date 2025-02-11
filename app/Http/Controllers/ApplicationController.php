<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\JobPost;
use App\Models\Business;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $applications = $business->applications()->with(['applicant', 'jobPost', 'createdBy'])->paginate(10);
        $view = view('job-applications._job_applications_table', compact('applications'))->render();
        return RequestResponse::ok('Ok', $view);
    }

    public function store(Request $request)
    {
        Log::debug($request->all());
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
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

            return RequestResponse::created('Application submitted successfully');
        });
    }

    public function show(Application $application)
    {
        return view('job-applications.show', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        $request->validate([
            'cover_letter' => 'nullable|string',
            'stage' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($request, $application) {
            $application->update($request->only(['cover_letter', 'stage']));
            return RequestResponse::ok('Application updated successfully', $application);
        });
    }

    public function destroy(Application $application)
    {
        return $this->handleTransaction(function () use ($application) {
            $application->delete();
            return RequestResponse::ok('Application deleted successfully');
        });
    }
}
