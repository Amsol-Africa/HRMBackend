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

class JobPostController extends Controller
{
    use HandleTransactions;

    public function index()
    {
        return view('job-posts.index', ['page' => 'Job Posts']);
    }

    public function create()
    {
        return view('job-posts.create', ['page' => 'Create Job Post']);
    }

    public function editView($business, $jobpost)
    {
        $jobPost = JobPost::findBySlug($jobpost);
        if (!$jobPost) {
            return redirect()->route('business.recruitment.jobs.index', $business)->with('error', 'Job post not found.');
        }
        return view('job-posts.edit', compact('jobPost'));
    }

    public function fetch(Request $request)
    {
        $businessSlug = session('active_business_slug');
        $query = JobPost::with('business')->orderBy('created_at', 'desc');

        if ($businessSlug) {
            $business = Business::findBySlug($businessSlug);
            $query->where('business_id', $business->id);
        }

        if ($request->has('filter')) {
            $filter = $request->input('filter');
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', "%$filter%")
                    ->orWhere('place', 'like', "%$filter%")
                    ->orWhere('employment_type', 'like', "%$filter%");
            });
        }

        $job_posts = $query->get();

        $jobPostsTable = view('job-posts._table', compact('job_posts'))->render();
        return RequestResponse::ok('Ok', $jobPostsTable);
    }

    public function fetchPublic(Request $request)
    {
        $job_posts = JobPost::where('is_public', true)
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('closing_date')
                    ->orWhere('closing_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return RequestResponse::ok('Ok', $job_posts);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'number_of_positions' => 'required|integer|min:1',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'place' => 'required|string',
            'closing_date' => 'nullable|date',
            'is_public' => 'boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $jobPost = $business->jobPosts()->create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'requirements' => $validatedData['requirements'] ?? null,
                'salary_range' => $validatedData['salary_range'] ?? null,
                'number_of_positions' => $validatedData['number_of_positions'],
                'employment_type' => $validatedData['employment_type'],
                'place' => $validatedData['place'],
                'created_by' => $user->id,
                'status' => Status::DRAFT,
                'closing_date' => $validatedData['closing_date'] ?? null,
                'is_public' => $validatedData['is_public'] ?? false,
            ]);

            return RequestResponse::created('Job Post created successfully.', ['job_post' => $jobPost]);
        });
    }

    public function show($business, $jobpost)
    {
        $jobPost = JobPost::with('business', 'location', 'creator')
            ->where('slug', $jobpost)
            ->firstOrFail();

        if ($jobPost->business->slug !== $business) {
            return RequestResponse::badRequest('Job post does not belong to this business.');
        }

        if (request()->is('api/*')) {
            return RequestResponse::ok('Ok', $jobPost);
        }

        return view('job-posts.show', compact('jobPost'));
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate(['job_post' => 'required|exists:job_posts,id']);
        $jobPost = JobPost::findOrFail($validatedData['job_post']);
        $jobPostForm = view('job-posts._form', compact('jobPost'))->render();
        return RequestResponse::ok('Ok', $jobPostForm);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'job_post_slug' => 'required|exists:job_posts,slug',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'number_of_positions' => 'required|integer|min:1',
            'employment_type' => 'required|string|in:full-time,part-time,contract,internship',
            'place' => 'required|string',
            'closing_date' => 'nullable|date',
            'status' => 'nullable|string|in:draft,open,closed',
            'is_public' => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::where('slug', $validatedData['job_post_slug'])->firstOrFail();
            $jobPost->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'requirements' => $validatedData['requirements'] ?? null,
                'salary_range' => $validatedData['salary_range'] ?? null,
                'number_of_positions' => $validatedData['number_of_positions'],
                'employment_type' => $validatedData['employment_type'],
                'place' => $validatedData['place'],
                'closing_date' => $validatedData['closing_date'] ?? null,
                'status' => $validatedData['status'] ?? $jobPost->status,
                'is_public' => $validatedData['is_public'] ?? $jobPost->is_public,
            ]);

            return RequestResponse::ok('Job Post updated successfully.', ['job_post' => $jobPost]);
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate(['job_post' => 'required|exists:job_posts,slug']);
        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::where('slug', $validatedData['job_post'])->firstOrFail();
            if ($jobPost->applications()->exists()) {
                $jobPost->applications()->delete();
            }
            $jobPost->delete();
            return RequestResponse::ok('success', ['message' => 'Job Post and related applications deleted successfully.']);
        });
    }

    public function togglePublic(Request $request)
    {
        $validatedData = $request->validate(['job_post' => 'required|exists:job_posts,slug']);
        return $this->handleTransaction(function () use ($validatedData) {
            $jobPost = JobPost::where('slug', $validatedData['job_post'])->firstOrFail();
            $jobPost->update(['is_public' => !$jobPost->is_public]);
            return RequestResponse::ok("success", ['message' => 'Job Post status changed succesfully.']);
        });
    }

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

                $cleanText = preg_replace('/^```html\s*/', '', $generatedText);
                $cleanText = preg_replace('/^```\s*/', '', $cleanText);
                $cleanText = preg_replace('/```\s*$/', '', $cleanText);

                return RequestResponse::ok('success', ['description' => $cleanText]);
            } else {
                return RequestResponse::badRequest('API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            return RequestResponse::badRequest('Failed to generate description: ' . $e->getMessage());
        }
    }
}
