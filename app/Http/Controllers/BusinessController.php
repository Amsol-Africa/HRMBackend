<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Module;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BusinessController extends Controller
{
    use HandleTransactions;
    public function create(Request $request)
    {
        $page = "Business Setup";
        $description = "Fill in your business details to get started with your account.";
        return view('auth.business-setup', compact('page', 'description'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company_size' => 'required|string',
            'industry' => 'required|string',
            'phone' => 'required|string|max:15',
            'country' => 'required|string',
            'code' => 'required|string|max:4',
            'logo' => 'nullable|file|image|max:1024',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {

            $countryCode = $validatedData['code'];
            $phoneNumber = "+{$countryCode}{$validatedData['phone']}";
            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:businesses,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            $user = auth()->user();

            $business = Business::create([
                'user_id' => $user->id,
                'company_name' => $validatedData['name'],
                'company_size' => $validatedData['company_size'],
                'industry' => $validatedData['industry'],
                'phone' => $phoneNumber,
                'code' => $validatedData['code'],
                'country' => $validatedData['country'],
            ]);

            if ($request->hasFile('logo')) {
                $business->addMediaFromRequest('logo')->toMediaCollection('businesses');
            }

            if (!$request->hasFile('logo')) {
                $business->addMediaFromBase64(createAvatarImageFromName($validatedData['name']))->toMediaCollection('businesses');
            }

            $business->setStatus(Status::MODULE);

            $user->setStatus(Status::MODULE);

            $redirect_url = route('setup.modules');

            return RequestResponse::created('Business registered successfully.', ['redirect_url' => $redirect_url]);
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

            session(['active_business_slug' => $business->slug]);

            $redirect_url = route('business.index', $business);

            $user->setStatus(Status::ACTIVE);
            $business->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Modules saved successfully.', ['redirect_url' => $redirect_url]);
        });
    }


}
