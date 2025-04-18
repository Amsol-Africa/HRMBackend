<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ContactSubmission;
use App\Traits\HandleTransactions;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContactSubmissionController extends Controller
{
    use HandleTransactions;

    public function externalStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'api_token' => 'required|string',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
                'company_name' => 'nullable|string|max:255',
                'country' => 'required|string|max:100',
                'inquiry_type' => 'required|string|in:General,Support,Sales,Partnership',
                'message' => 'required|string|max:5000',
            ], [
                'phone.regex' => 'The phone number must be a valid international format (e.g., +1234567890).',
                'inquiry_type.in' => 'The inquiry type must be one of: General, Support, Sales, Partnership.',
            ]);

            $business = Business::where('slug', 'amsol')->first();

            if (!$business) {
                Log::warning('Business with slug amsol not found', [
                    'api_token' => Str::mask($validated['api_token'], '*', 4, -4),
                ]);
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            if (!$business->api_token) {
                Log::warning('No API token set for amsol', [
                    'business_id' => $business->id,
                ]);
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            try {
                if (!Hash::check($validated['api_token'], $business->api_token)) {
                    Log::warning('Invalid API token for amsol', [
                        'business_id' => $business->id,
                        'api_token' => Str::mask($validated['api_token'], '*', 4, -4),
                    ]);
                    return RequestResponse::unauthorized('Invalid or unauthorized API token.');
                }
            } catch (\Exception $e) {
                Log::error('Token verification failed: ' . $e->getMessage(), [
                    'business_id' => $business->id,
                    'api_token' => Str::mask($validated['api_token'], '*', 4, -4),
                ]);
                return RequestResponse::unauthorized('Invalid or unauthorized API token.');
            }

            return $this->handleTransaction(function () use ($validated, $business) {
                $submission = ContactSubmission::create([
                    'business_id' => $business->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'company_name' => $validated['company_name'],
                    'country' => $validated['country'],
                    'inquiry_type' => $validated['inquiry_type'],
                    'message' => $validated['message'],
                    'status' => 'pending',
                ]);

                Log::info('Contact submission created', [
                    'submission_id' => $submission->id,
                    'email' => $validated['email'],
                    'business_id' => $business->id,
                ]);

                return RequestResponse::ok('Contact submission received successfully', [
                    'submission_id' => $submission->id,
                    'status' => $submission->status,
                ]);
            }, function ($exception) {
                Log::error('Contact submission failed: ' . $exception->getMessage(), [
                    'email' => $validated['email'],
                ]);
                return RequestResponse::badRequest('An error occurred while processing your submission: ' . $exception->getMessage());
            });
        } catch (ValidationException $e) {
            return RequestResponse::badRequest('Validation failed', $e->errors());
        } catch (\Exception $e) {
            Log::error('Unexpected error in externalStore: ' . $e->getMessage(), [
                'request' => $request->except('api_token'),
            ]);
            return RequestResponse::badRequest('An unexpected error occurred.');
        }
    }
}
