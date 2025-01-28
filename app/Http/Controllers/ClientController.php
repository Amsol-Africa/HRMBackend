<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Client;
use App\Models\Business;
use App\Mail\InviteUserMail;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\AccessRequest;
use App\Mail\AccessRequestMail;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class ClientController extends Controller
{
    use HandleTransactions;
    public function fetch(Request $request)
    {
        $business_slug = session('active_business_slug');
        $business = Business::findBySlug($business_slug);

        $managed_businesses = Client::where('employee_id', $request->user()->id)
        ->with('managedBusiness')
        ->get()
        ->pluck('managedBusiness');

        $managed_businesses_cards = view('clients._clients_table', compact('managed_businesses'))->render();

        return RequestResponse::ok('Ok', $managed_businesses_cards);
    }
    public function requestAccess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        return $this->handleTransaction(function () use ($request) {

            $email = $request->input('email');
            $requester = $request->user();
            $business_id = Business::findBySlug(session('active_business_slug'))->id;
            $existingUser = User::where('email', $email)->first();

            $registrationToken = generateRegistrationToken($requester->id, $business_id);

            $accessRequest = AccessRequest::create([
                'requester_id' => $requester->id,
                'business_id' => $business_id,
                'email' => $email,
                'registration_token' => $registrationToken,
            ]);

            $accessRequest->setStatus(Status::PENDING);

            if ($existingUser && $existingUser->business) {
                Mail::to($existingUser->email)->send(new AccessRequestMail($accessRequest));
            } else {
                Mail::to($email)->send(new InviteUserMail($accessRequest));
            }

            return RequestResponse::created('Access request sent successfully');
        });
    }

    public function impersonateManagedBusiness(Request $request)
    {
        $request->validate([
            'business_slug' => 'required|string:exists:businesses,slug',
        ]);

        $managedBusiness = Business::findBySlug($request->input('business_slug'));

        $manager = Client::where('employee_id', $request->user()->id)
            ->where('client_business', $managedBusiness->id)
            ->first();

        if (!$manager) {
            return RequestResponse::badRequest('Unauthorized access to impersonate this business');
        }

        if (!$managedBusiness) {
            return RequestResponse::badRequest('Managed business not found');
        }

        session(['active_business_slug' => $managedBusiness->slug]);

        View::share('currentBusiness', $managedBusiness);

        return RequestResponse::ok(
            message: "Welcome to {$managedBusiness->business_name} dashboard",
            data: ['redirect_url' => route('business.index', $managedBusiness)]
        );
    }

}
