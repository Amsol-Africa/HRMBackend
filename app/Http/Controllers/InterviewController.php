<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\User;
use App\Models\Business;
use App\Models\Interview;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InterviewController extends Controller
{
    use HandleTransactions;

    /**
     * Display a listing of interviews.
     */
    public function index()
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $interviews = $business->interviews()->with(['application.applicant', 'interviewer'])->paginate(10);

        return view('interviews.index', compact('interviews'));
    }

    /**
     * Store a newly created interview.
     */
    public function store(Request $request)
    {
        Log::debug($request->all());

        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'interviewer_id' => 'nullable|exists:users,id',
            'type' => 'required|in:phone,video,in-person',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|required_if:type,in-person|string',
            'meeting_link' => 'nullable|required_if:type,video|url',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($request) {
            $application = Application::findOrFail($request->application_id);
            $business = $application->business;

            $interview = Interview::create([
                'application_id' => $application->id,
                'interviewer_id' => $request->interviewer_id,
                'created_by' => Auth::id(),
                'type' => $request->type,
                'scheduled_at' => $request->scheduled_at,
                'location' => $request->location,
                'meeting_link' => $request->meeting_link,
                'notes' => $request->notes,
            ]);

            $interview->setStatus(Status::SCHEDULED);

            return RequestResponse::created('Interview scheduled successfully');
        });
    }

    /**
     * Display the specified interview details.
     */
    public function show(Interview $interview)
    {
        return view('interviews.show', compact('interview'));
    }

    /**
     * Update the specified interview.
     */
    public function update(Request $request, Interview $interview)
    {
        $request->validate([
            'interviewer_id' => 'nullable|exists:users,id',
            'type' => 'required|in:phone,video,in-person',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|required_if:type,in-person|string',
            'meeting_link' => 'nullable|required_if:type,video|url',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($request, $interview) {
            $interview->update($request->only([
                'interviewer_id', 'type', 'scheduled_at', 'location', 'meeting_link', 'notes'
            ]));

            return RequestResponse::ok('Interview updated successfully', $interview);
        });
    }

    /**
     * Remove the specified interview.
     */
    public function destroy(Interview $interview)
    {
        return $this->handleTransaction(function () use ($interview) {
            $interview->delete();
            return RequestResponse::ok('Interview deleted successfully');
        });
    }
}
