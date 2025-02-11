<?php
namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApplicantController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $applicants = Applicant::with('user')->get();
        $applicant_table = view('job-applicants.applicants._table', compact('applicants'))->render();

        return RequestResponse::ok('Ok', $applicant_table);
    }

    public function create()
    {
        $users = User::all();
        return view('recruitment.applicants.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'midle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'code' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'linkedin_profile' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'current_job_title' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|max:50',
            'education_level' => 'nullable|string|max:50',
            'desired_salary' => 'nullable|numeric',
            'job_preferences' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
        ]);

        return $this->handleTransaction(function () use ($request) {

            $countryCode = $request->code;
            $phoneNumber = "+{$countryCode}{$request->phone}";

            $validator = Validator::make(['phone' => $phoneNumber], [
                'phone' => 'unique:users,phone',
            ]);

            throw_if($validator->fails(), ValidationException::class, $validator);
            // Create User

            $name = $request->first_name . " " . $request->middle_name . " " . $request->last_name;

            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'phone' => $phoneNumber,
                'password' => Hash::make($request->password),
                'country' => $request->country,
            ]);
            $user->assignRole('applicant');
            $user->setStatus(Status::ACTIVE);

            $request->hasFile('image')
                ? $user->addMediaFromRequest('image')->toMediaCollection('avatars')
                : $user->addMediaFromBase64(createAvatarImageFromName($request->name))->toMediaCollection('avatars');


            // Create Applicant Profile
            $applicant = Applicant::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'linkedin_profile' => $request->linkedin_profile,
                'portfolio_url' => $request->portfolio_url,
                'current_job_title' => $request->current_job_title,
                'current_company' => $request->current_company,
                'experience_level' => $request->experience_level,
                'education_level' => $request->education_level,
                'desired_salary' => $request->desired_salary,
                'job_preferences' => $request->job_preferences,
                'source' => $request->source,
                'created_by' => auth()->id(),
            ]);

            $applicant->setStatus(Status::ACTIVE);
            return RequestResponse::created('Applicant created successfully');

        });

    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
        ]);

        $applicant = Applicant::with('user')->findOrFail($validatedData['applicant_id']);
        $users = User::all();
        $applicant_form = view('recruitment.applicants._form', compact('applicant', 'users'))->render();

        return RequestResponse::ok('Ok', $applicant_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'linkedin_profile' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'summary' => 'nullable|string',
            'current_job_title' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|in:Entry-level,Mid-level,Senior',
            'education_level' => 'nullable|string|in:High School,Bachelor\'s,Master\'s,PhD',
            'desired_salary' => 'nullable|string',
            'job_preferences' => 'nullable|string',
            'source' => 'nullable|string|max:255',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $applicant = Applicant::findOrFail($validatedData['applicant_id']);
            $applicant->update($validatedData);
            $applicant->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Applicant updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $applicant = Applicant::findOrFail($validatedData['applicant_id']);

            if ($applicant) {
                $applicant->delete();
                return RequestResponse::ok('Applicant deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete applicant.', 404);
        });
    }
}
