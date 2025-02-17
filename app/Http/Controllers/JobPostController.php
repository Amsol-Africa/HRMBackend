<?php
namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class JobPostController extends Controller
{
    use HandleTransactions;
    
    // Fetch Job Posts
    public function fetch(Request $request)
    {
        $businessSlug = session('active_business_slug');

        if ($businessSlug) {
            $business = Business::findBySlug($businessSlug);
            $job_posts = JobPost::with('business')->where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
        } else {
            $job_posts = JobPost::with('business')->orderBy('created_at', 'desc')->get();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return RequestResponse::ok('Ok', $job_posts);
        }

        $jobPostsTable = view('job-posts._job_posts_table', compact('job_posts'))->render();
        return RequestResponse::ok('Ok', $jobPostsTable);
    }

    // Store New Job Post
    public function store(Request $request)
    {   Log::debug($request->all());
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary_range' => 'nullable|string',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'place' => 'required|string',
            'posted_at' => 'nullable|date',
            'closed_at' => 'nullable|date|after_or_equal:posted_at',
        ]);

        if (!empty($validatedData['salary_range'])) {
            $validatedData['salary_range'] = preg_replace('/,/', '', $validatedData['salary_range']);
        }

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $jobPost = $business->jobPosts()->create([
                'place' => $validatedData['place'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'salary_range' => $validatedData['salary_range'] ?? null,
                'employment_type' => $validatedData['employment_type'],
                'created_by' => $user->id,
                'posted_at' => $validatedData['posted_at'] ?? now(),
                'closed_at' => $validatedData['closed_at'] ?? null,
            ]);

            $jobPost->setStatus(Status::OPEN);

            return RequestResponse::created('Job Post created successfully.');
        });
    }

    // Store New Job Post
    public function add(Request $request)
    {   Log::debug($request->all());
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary_range' => 'nullable|string',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'place' => 'required|string',
            'posted_at' => 'nullable|date',
            'closed_at' => 'nullable|date|after_or_equal:posted_at',
        ]);

        if (!empty($validatedData['salary_range'])) {
            $validatedData['salary_range'] = preg_replace('/,/', '', $validatedData['salary_range']);
        }

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $jobPost = $business->jobPosts()->create([
                'place' => $validatedData['place'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'salary_range' => $validatedData['salary_range'] ?? null,
                'employment_type' => $validatedData['employment_type'],
                'created_by' => $user->id,
                'posted_at' => $validatedData['posted_at'] ?? now(),
                'closed_at' => $validatedData['closed_at'] ?? null,
            ]);

            $jobPost->setStatus(Status::OPEN);

            return RequestResponse::created('Job Post created successfully.');
        });
    }

    // Edit Job Post
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'job_post' => 'required|exists:job_posts,id',
        ]);

        $jobPost = JobPost::findOrFail($validatedData['job_post']);
        $jobPostForm = view('recruitment.job-posts._form', compact('jobPost'))->render();

        return RequestResponse::ok('Ok', $jobPostForm);
    }

    // Update Job Post
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'location_id' => 'required|exists:locations,id',
            'posted_at' => 'nullable|date',
            'closed_at' => 'nullable|date|after_or_equal:posted_at',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::findOrFail($validatedData['job_post_id']);

            $jobPost->update([
                'location_id' => $validatedData['location_id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'requirements' => $validatedData['requirements'] ?? null,
                'salary_range' => $validatedData['salary_range'] ?? null,
                'employment_type' => $validatedData['employment_type'],
                'posted_at' => $validatedData['posted_at'],
                'closed_at' => $validatedData['closed_at'],
            ]);

            return RequestResponse::ok('Job Post updated successfully.', ['job_openings' => $jobPost]);
        });
    }

    // Delete Job Post
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::findOrFail($validatedData['job_post_id']);

            if ($jobPost) {
                $jobPost->delete();
                return RequestResponse::ok('Job Post deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete job post.', 404);
        });
    }
}