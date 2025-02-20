<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class JobCategoryController extends Controller
{
    use HandleTransactions;
    public function fetch(Request $request)
    {
        $user = $request->user();
        $business = Business::findBySlug(slug: session('active_business_slug'));
        $job_categories = $business->job_categories;
        $job_categories_table = view('job-categories._table', compact('job_categories'))->render();
        return RequestResponse::ok('Ok', $job_categories_table);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'job_category' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $job_category = $business->job_categories()->create([
                'name' => $validatedData['job_category'],
                'description' => $validatedData['description'] ?? null,
            ]);

            $job_category->setStatus(Status::ACTIVE);

            return RequestResponse::created('Job Category Added successfully.');
        });
    }
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'job_category' => 'required|string|exists:job_categories,slug',
        ]);

        $job_category = JobCategory::findBySlug($validatedData['job_category']);
        $job_categories_form = view('job-categories._form', compact('job_category'))->render();
        return RequestResponse::ok('Ok', $job_categories_form);
    }
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'job_category_slug' => 'required|exists:job_categories,slug',
            'job_category' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {

            $job_category = JobCategory::findBySlug($validatedData['job_category_slug']);

            $job_category->update([
                'name' => $validatedData['job_category'],
                'description' => $validatedData['description'] ?? null,
            ]);

            $job_category->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Job Category Updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'job_category' => 'required|exists:job_categories,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {

            $job_category = JobCategory::findBySlug($validatedData['job_category']);

            if ($job_category) {
                $job_category->delete();
                return RequestResponse::ok('Job category deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete job category.', 404);
        });
    }

}