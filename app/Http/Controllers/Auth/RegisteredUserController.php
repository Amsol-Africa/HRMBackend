<?php

namespace App\Http\Controllers\Auth;

use App\Enum\Status;
use App\Models\User;
use App\Models\Module;
use App\Models\Business;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
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
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
    public function setup(): View
    {
        return view('auth.business-setup');
    }
    public function modules(): View
    {
        $modules = Module::all();
        return view('auth.modules-setup', compact('modules'));
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
            'code' => 'required|string|max:15',
            'country' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $countryCode = $request->code;
            $phoneNumber = "+{$countryCode}{$request->phone}";
            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);

            // Create User
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $phoneNumber,
                'code' => $validatedData['code'],
            ]);

            // Assign business_owner role
            $user->assignRole('business_owner');

            $user->setStatus(Status::ACTIVE);

            $request->hasFile('image')
                ? $user->addMediaFromRequest('image')->toMediaCollection('avatars')
                : $user->addMediaFromBase64(createAvatarImageFromName($request->name))->toMediaCollection('avatars');

            DB::commit();

            $user->sendEmailVerificationNotification();

            auth()->login($user);

            $redirect_url = route('setup.modules');

            return RequestResponse::created('Account created successfully.', ['redirect_url' => $redirect_url]);

        } catch (ValidationException $e) {
            DB::rollBack();
            report($e);
            Log::error($e->getCode() . " " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return back()->withErrors($validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            Log::error($e->getCode() . " " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return back()->with('error', 'An unexpected error occurred. Please try again.')->withInput();
        }
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

    public function acceptInvitation(Request $request, $token)
    {
        $invitation = BusinessInvitation::where('token', $token)->where('expires_at', '>', now())->firstOrFail();

        $validatedData = $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => Hash::make($validatedData['password']),
                'business_id' => $invitation->business_id,
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($invitation->role);

            // Create employee record
            $lastEmpId = Employee::where('business_id', $invitation->business_id)->max('employee_id');
            $nextEmpId = 'EMP' . str_pad((intval(substr($lastEmpId, 3)) + 1), 3, '0', STR_PAD_LEFT);

            $user->employee()->create([
                'business_id' => $invitation->business_id,
                'employee_id' => $nextEmpId,
                'department' => $invitation->department,
                'position' => $invitation->position,
                'start_date' => now(),
            ]);

            // Mark invitation as used
            $invitation->update(['accepted_at' => now()]);

            DB::commit();

            auth()->login($user);

            return redirect()->route('dashboard')
                           ->with('success', 'Welcome to ' . $user->business->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to accept invitation. Please try again.']);
        }
    }
}
