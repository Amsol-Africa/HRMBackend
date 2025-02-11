<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function reschedule(Request $request, Interview $interview)
    {
        $request->validate(['scheduled_at' => 'required|date|after:now']);

        $interview->reschedule($request->scheduled_at);

        return response()->json(['message' => 'Interview rescheduled successfully']);
    }

    public function cancel(Request $request, Interview $interview)
    {
        $request->validate(['reason' => 'required|string']);

        $interview->cancel($request->reason);

        return response()->json(['message' => 'Interview canceled successfully']);
    }
}
