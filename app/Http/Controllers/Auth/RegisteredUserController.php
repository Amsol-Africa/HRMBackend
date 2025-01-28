<?php

namespace App\Http\Controllers\Auth;

use App\Enum\Status;
use App\Models\AccessRequest;
use App\Models\User;
use App\Models\Client;
use App\Models\Module;
use App\Models\Business;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use Illuminate\Validation\Rules;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    use HandleTransactions;
    public function create(Request $request,  $registration_token = null)
    {
        if (auth()->check() && !empty($registration_token)) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        $page = "Create an Account";
        $description = "Register to access your personalized dashboard and services.";
        return view('auth.register', compact('page', 'description', 'registration_token'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'code' => 'required|string|max:15',
            'country' => 'required|string',
            'registration_token' => 'nullable|string:exists:access_requests,registration_token',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {

            $countryCode = $request->code;
            $phoneNumber = "+{$countryCode}{$request->phone}";

            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            if (!empty($validatedData['registration_token'])) {
                $invitation = AccessRequest::where('registration_token', $validatedData['registration_token'])->firstOrFail();
                Log::debug($invitation);
                $managing_business = Business::find($invitation->business_id);
                Log::debug($managing_business);
                session(['managing_business' => $managing_business->id]);
                session(['employee_id' => $invitation->requester_id]);
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $phoneNumber,
                'code' => $validatedData['code'],
                'country' => $validatedData['country'],
            ]);

            $user->assignRole('business_owner');
            $user->setStatus(Status::SETUP);

            $request->hasFile('image')
                ? $user->addMediaFromRequest('image')->toMediaCollection('avatars')
                : $user->addMediaFromBase64(createAvatarImageFromName($request->name))->toMediaCollection('avatars');

            $user->sendEmailVerificationNotification();

            Auth::login($user);

            $redirect_url = route('setup.business');

            return RequestResponse::created('Account created successfully.', ['redirect_url' => $redirect_url]);
        });
    }

    public function setupModules(Request $request)
    {
        $validatedData = $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'exists:modules,id'
        ]);

        $business = auth()->user()->business;

        // Attach selected modules
        foreach ($validatedData['modules'] as $moduleId) {
            $business->modules()->attach($moduleId, [
                'is_active' => true,
                'subscription_ends_at' => now()->addDays(14)
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function inviteTeamMember(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'role' => 'required|exists:roles,name',
            'department' => 'required|string',
            'position' => 'required|string'
        ]);

        $business = auth()->user()->business;

        // Generate invitation token
        $token = Str::random(32);

        // Store invitation
        $invitation = $business->invitations()->create([
            'email' => $validatedData['email'],
            'name' => $validatedData['name'],
            'role' => $validatedData['role'],
            'token' => $token,
            'department' => $validatedData['department'],
            'position' => $validatedData['position'],
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7)
        ]);

        // Send invitation email
        Mail::to($validatedData['email'])->send(new TeamInvitation($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }
}
