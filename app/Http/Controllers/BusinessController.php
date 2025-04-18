<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Client;
use App\Models\Module;
use App\Models\Business;
use App\Models\Industry;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use App\Notifications\BusinessChangedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class BusinessController extends Controller
{
    use HandleTransactions;

    public function create(Request $request)
    {
        $page = "Business Setup";
        $description = "Fill in your business details to get started with your account.";
        $industries = Industry::all();
        $user = auth()->user();
        $business = Business::where('user_id', $user->id)->first();

        return view('auth.business-setup', compact('page', 'description', 'industries', 'business'));
    }

    public function store(Request $request)
    {
        Log::info('Reached BusinessController@store', ['user_id' => auth()->id(), 'input' => $request->all()]);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:businesses,company_name',
            'company_size' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'code' => 'required|string|max:4',
            'registration_no' => 'required|string|max:255|unique:businesses,registration_no',
            'tax_pin_no' => 'required|string|max:255|unique:businesses,tax_pin_no',
            'business_license_no' => 'required|string|max:255|unique:businesses,business_license_no',
            'physical_address' => 'required|string|max:255',
            'logo' => 'required|file|image|max:1024',
            'registration_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
            'tax_pin_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
            'business_license_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            try {
                $countryCode = $validatedData['code'];
                $phoneNumber = "+{$countryCode}{$validatedData['phone']}";
                $validator = Validator::make(['phone' => $phoneNumber], [
                    'phone' => 'unique:businesses,phone',
                ]);

                throw_if($validator->fails(), ValidationException::withMessages($validator->errors()->all()));

                $user = auth()->user();

                // Create new business
                $business = Business::create([
                    'user_id' => $user->id,
                    'company_name' => $validatedData['name'],
                    'company_size' => $validatedData['company_size'],
                    'industry' => $validatedData['industry'],
                    'phone' => $phoneNumber,
                    'code' => $validatedData['code'],
                    'country' => $validatedData['country'],
                    'registration_no' => $validatedData['registration_no'],
                    'tax_pin_no' => $validatedData['tax_pin_no'],
                    'business_license_no' => $validatedData['business_license_no'],
                    'physical_address' => $validatedData['physical_address'],
                    'verified' => false,
                ]);

                // Handle logo upload
                $business->clearMediaCollection('businesses');
                $business->addMediaFromRequest('logo')->toMediaCollection('businesses');

                // Handle other file uploads
                if ($request->hasFile('registration_certificate')) {
                    $business->clearMediaCollection('registration_certificates');
                    $business->addMediaFromRequest('registration_certificate')->toMediaCollection('registration_certificates');
                }
                if ($request->hasFile('tax_pin_certificate')) {
                    $business->clearMediaCollection('tax_pin_certificates');
                    $business->addMediaFromRequest('tax_pin_certificate')->toMediaCollection('tax_pin_certificates');
                }
                if ($request->hasFile('business_license_certificate')) {
                    $business->clearMediaCollection('business_license_certificates');
                    $business->addMediaFromRequest('business_license_certificate')->toMediaCollection('business_license_certificates');
                }

                $business->setStatus(Status::MODULE);

                if (session()->has('managing_business') && session()->has('employee_id')) {
                    $business_id = session('managing_business');
                    $employee_id = session('employee_id');
                    Client::create([
                        'business_id' => $business_id,
                        'client_business' => $business->id,
                        'employee_id' => $employee_id,
                    ])->setStatus(Status::ACTIVE);
                }

                $user->setStatus(Status::MODULE);
                $this->notifyBusinessOwner($business, $user, 'created');

                $redirect_url = route('setup.modules');

                Log::info('Business created successfully', [
                    'business_id' => $business->id,
                    'redirect_url' => $redirect_url
                ]);

                return RequestResponse::created('Business registered successfully.', [
                    'redirect_url' => $redirect_url,
                    'business' => $business->fresh(['media'])
                ]);
            } catch (\Exception $e) {
                Log::error('Business store failed: ' . $e->getMessage(), ['exception' => $e]);
                throw $e;
            }
        });
    }

    public function saveModules(Request $request)
    {
        $validatedData = $request->validate([
            'business_slug' => 'required|exists:businesses,slug',
            'modules' => 'required|array',
            'modules.*' => 'exists:modules,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $request) {
            $user = $request->user();
            $business = Business::findBySlug($validatedData['business_slug']);

            $moduleIds = Module::whereIn('slug', $validatedData['modules'])->pluck('id');
            $business->modules()->sync($moduleIds);

            session()->forget(['managing_business', 'employee_id']);
            session(['active_business_slug' => $business->slug]);

            $user->setStatus(Status::ACTIVE);
            $business->setStatus(Status::ACTIVE);

            $redirect_url = $business->verified ? route('business.index', $business) : route('business.activate', $business->slug);

            return RequestResponse::ok('Modules saved successfully.', ['redirect_url' => $redirect_url, 'business' => $business]);
        });
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'business_slug' => 'required|string|exists:businesses,slug',
            'name' => 'required|string|max:255|unique:businesses,company_name,' . Business::findBySlug($request->business_slug)->id,
            'company_size' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'country' => 'required|string|max:255',
            'code' => 'required|string|max:4',
            'registration_no' => 'required|string|max:255|unique:businesses,registration_no,' . Business::findBySlug($request->business_slug)->id,
            'tax_pin_no' => 'required|string|max:255|unique:businesses,tax_pin_no,' . Business::findBySlug($request->business_slug)->id,
            'business_license_no' => 'required|string|max:255|unique:businesses,business_license_no,' . Business::findBySlug($request->business_slug)->id,
            'physical_address' => 'required|string|max:255',
            'logo' => 'nullable|file|image|max:1024',
            'registration_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
            'tax_pin_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
            'business_license_certificate' => 'nullable|file|mimes:pdf,image|max:2048',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $countryCode = $validatedData['code'];
            $phoneNumber = "+{$countryCode}{$validatedData['phone']}";
            $business = Business::findBySlug($validatedData['business_slug']);

            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:businesses,phone,' . $business->id,
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $business->update([
                'company_name' => $validatedData['name'],
                'company_size' => $validatedData['company_size'],
                'industry' => $validatedData['industry'],
                'phone' => $phoneNumber,
                'code' => $validatedData['code'],
                'country' => $validatedData['country'],
                'registration_no' => $validatedData['registration_no'],
                'tax_pin_no' => $validatedData['tax_pin_no'],
                'business_license_no' => $validatedData['business_license_no'],
                'physical_address' => $validatedData['physical_address'],
            ]);

            if ($request->hasFile('logo')) {
                $business->clearMediaCollection('businesses');
                $business->addMediaFromRequest('logo')->toMediaCollection('businesses');
            }
            if ($request->hasFile('registration_certificate')) {
                $business->clearMediaCollection('registration_certificates');
                $business->addMediaFromRequest('registration_certificate')->toMediaCollection('registration_certificates');
            }
            if ($request->hasFile('tax_pin_certificate')) {
                $business->clearMediaCollection('tax_pin_certificates');
                $business->addMediaFromRequest('tax_pin_certificate')->toMediaCollection('tax_pin_certificates');
            }
            if ($request->hasFile('business_license_certificate')) {
                $business->clearMediaCollection('business_license_certificates');
                $business->addMediaFromRequest('business_license_certificate')->toMediaCollection('business_license_certificates');
            }

            $this->notifyBusinessOwner($business, auth()->user(), 'updated');

            $redirect_url = route('business.organization-setup', $business->slug);

            return RequestResponse::ok('Business updated successfully.', [
                'success' => true,
                'redirect_url' => $redirect_url,
                'business' => $business->fresh(['media'])
            ]);
        });
    }

    public function activate(Request $request, $slug)
    {
        $business = Business::findBySlug($slug);
        if (!$business) {
            abort(404, 'Business not found');
        }

        $page = "Activate Your Business";
        $description = "Your business is pending activation. Please ensure all required documents are uploaded.";
        $industries = Industry::all();

        return view('business.activate', compact('business', 'page', 'description', 'industries'));
    }

    public function setup($slug)
    {
        $business = Business::findBySlug($slug);
        if (!$business) {
            abort(404, 'Business not found');
        }

        $page = "Business Setup";
        $description = "Update your business details here.";
        $industries = Industry::all();

        return view('business.setup', compact('business', 'page', 'description', 'industries'));
    }

    public function fetch(Request $request)
    {
        $businesses = Business::where('user_id', auth()->id())->with('media')->get();
        return RequestResponse::ok('Businesses fetched successfully.', $businesses);
    }

    public function destroy(Request $request)
    {
        $request->validate(['slug' => 'required|exists:businesses,slug']);

        return $this->handleTransaction(function () use ($request) {
            $business = Business::findBySlug($request->slug);
            if ($business->user_id !== auth()->id()) {
                return RequestResponse::forbidden('Unauthorized to delete this business.');
            }

            $business->delete();
            return RequestResponse::ok('Business deleted successfully.');
        });
    }

    protected function notifyBusinessOwner($business, $user, $action = 'updated')
    {
        try {
            $user->notify(new BusinessChangedNotification($business, $user, $action));
            return RequestResponse::ok('Notification sent successfully.');
        } catch (TransportException $e) {
            Log::error('Failed to send notification email: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to send notification email.', [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error sending notification: ' . $e->getMessage());
            return RequestResponse::badRequest('An unexpected error occurred while sending the notification.', [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateApiToken(Request $request, $businessSlug)
    {
        return $this->handleTransaction(function () use ($businessSlug) {
            $business = Business::where('slug', $businessSlug)->firstOrFail();
            if ($business->user_id !== auth()->id()) {
                return RequestResponse::badRequest('Business not found or unauthorized.');
            }
            if ($businessSlug !== 'amsol') {
                return RequestResponse::badRequest('API token generation is restricted to amsol.');
            }
            do {
                $apiToken = Str::random(60);
            } while (Business::where('api_token', Hash::make($apiToken))->exists());

            $business->update([
                'api_token' => Hash::make($apiToken),
                'updated_at' => now(),
            ]);

            Log::info('API token generated', [
                'business_id' => $business->id,
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            session()->flash('api_token', $apiToken);
            session()->flash('api_token_warning', 'Previous API token is now invalid.');

            return redirect()->route('business.api-token', $businessSlug)
                ->with('message', 'API token generated successfully.');
        }, function ($exception) {
            Log::error('API token generation failed: ' . $exception->getMessage(), [
                'business_slug' => $businessSlug,
                'user_id' => auth()->id(),
            ]);
            return RequestResponse::badRequest('Failed to generate API token.');
        });
    }

    public function showApiTokenForm($businessSlug)
    {
        $business = Business::findBySlug($businessSlug);
        if (!$business || $business->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }
        return view('business.api-token', compact('business'));
    }
}
