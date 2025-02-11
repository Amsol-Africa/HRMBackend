<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enum\Status;
use App\Models\Business;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use App\Notifications\InterviewScheduledNotification;

class InterviewController extends Controller
{
    use HandleTransactions;

    public function store(Request $request)
    {
        Log::debug($request->all());
        $validatedData = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'type' => 'required|in:phone,video,in-person',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|string|required_if:type,in-person',
            'meeting_link' => 'nullable|url|required_if:type,video',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $interview = Interview::create(array_merge($validatedData, [
                'created_by' => auth()->id()
            ]));

            $interview->setStatus(Status::SCHEDULED);

            $application = Application::with('applicant')->findOrFail($validatedData['application_id']);

            $application->applicant->user->notify(new InterviewScheduledNotification($interview));

            if ($interview->interviewer) {
                $interview->interviewer->notify(new InterviewScheduledNotification($interview));
            }

            return RequestResponse::created('Interview scheduled successfully.');
        });
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        if (!$business) {
            return RequestResponse::badRequest('Business not found.', 404);
        }

        $interviews = Interview::whereHas('jobApplication', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->with('jobApplication', 'interviewer')->latest()->paginate(10);

        $interviews_table = view('job-applications._interviews_table', compact('interviews'))->render();

        return RequestResponse::ok('Interviews fetched successfully.', $interviews_table);
    }

    public function show($id)
    {
        $interview = Interview::with('application', 'interviewer')->findOrFail($id);
        return RequestResponse::ok('Interview details fetched successfully.', $interview);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:phone,video,in-person',
            'scheduled_at' => 'required|date|after:now',
            'location' => 'nullable|string|required_if:type,in-person',
            'meeting_link' => 'nullable|url|required_if:type,video',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $interview = Interview::findOrFail($id);
            $interview->update($validatedData);
            return RequestResponse::ok('Interview updated successfully.', $interview);
        });
    }

    public function reschedule(Request $request, $id)
    {
        $validatedData = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $interview = Interview::findOrFail($id);
            $interview->update(['scheduled_at' => $validatedData['scheduled_at']]);
            return RequestResponse::ok('Interview rescheduled successfully.', $interview);
        });
    }

    public function cancel($id)
    {
        return $this->handleTransaction(function () use ($id) {
            $interview = Interview::findOrFail($id);
            $interview->delete();
            return RequestResponse::ok('Interview canceled successfully.');
        });
    }
}
