<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Http;
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

        if ($request->is('api/*')) {
            return RequestResponse::ok('Ok', $job_posts);
        }

        $jobPostsTable = view('job-posts._job_posts_table', compact('job_posts'))->render();
        return RequestResponse::ok('Ok', $jobPostsTable);
    }

    // Store New Job Post
    public function store(Request $request)
    {
        Log::debug($request->all());
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

    // Store New Job Post (Duplicate method, consider merging with store)
    public function add(Request $request)
    {
        Log::debug($request->all());
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
            'job_post_slug' => 'required|exists:job_posts,id',
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
            $jobPost = JobPost::findOrFail($validatedData['job_post_slug']);

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
            'job_post' => 'required|exists:job_posts,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::findBySlug($validatedData['job_post']);

            if ($jobPost) {
                $jobPost->delete();
                return RequestResponse::ok('Job Post deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete job post.', 404);
        });
    }

    // Generate Job Description using Gemini API
    public function generateDescription(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'place' => 'required|string',
            'salary_range' => 'nullable|string',
        ]);

        $title = $validatedData['title'];
        $employmentType = $validatedData['employment_type'];
        $place = $validatedData['place'];
        $salaryRange = $validatedData['salary_range'];

        $apiKey = env('GOOGLE_GEMINI_API_KEY');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $prompt = "Generate a detailed, SEO-friendly job description in HTML format for a {$title} position, using the following details: " .
            "Employment type: {$employmentType}. " .
            "Location: {$place}. " .
            "Salary range: " . ($salaryRange ?: "Competitive") . ". " .
            "Use proper semantic HTML for better SEO and readability. " .
            "Ensure each section is keyword-rich, naturally incorporating the job title '{$title}' where relevant. " .
            "Format the content using: " .
            "<h2>Job Overview</h2> (describe the role and its purpose in a <p> tag), " .
            "<h2>Key Responsibilities</h2> (list 5-7 key duties in a <ul> with <li> tags), " .
            "<h2>Required Qualifications</h2> (list 5-7 qualifications/skills in a <ul> with <li> tags), " .
            "<h2>Benefits</h2> (list 3-5 benefits in a <ul> with <li> tags). " .
            "Ensure the HTML is well-structured for search engines and readability. " .
            "Return only the HTML content, starting with <h2> tags, without any additional text or instructions.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No content generated';

                // Remove unwanted formatting (```html at the start and ``` at the end)
                $cleanText = preg_replace('/^```html\s*/', '', $generatedText); // Remove ```html at the start
                $cleanText = preg_replace('/^```\s*/', '', $cleanText); // Remove standalone ```
                $cleanText = preg_replace('/```\s*$/', '', $cleanText); // Remove trailing ```

                return RequestResponse::ok('success', ['description' => $cleanText]);
            } else {
                return RequestResponse::badRequest('API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            return RequestResponse::badRequest('Failed to generate description: ' . $e->getMessage());
        }
    }
}
