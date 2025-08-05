<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('No active business selected.');
        }

        $logs = ActivityLog::forBusiness($business->id)
            ->latest()
            ->paginate(20);

        $logs_card = view('components.activities', compact('logs'))->render();
        return RequestResponse::ok("Activity logs fetched successfully.", $logs_card);
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'business_slug' => 'required|string|exists:businesses,slug',
        ]);

        $business = Business::findBySlug($request->business_slug);
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $logs = ActivityLog::forBusiness($business->id)
            ->latest()
            ->paginate(20);

        $logs_card = view('components.activities', compact('logs'))->render();

        return RequestResponse::ok("Activity logs fetched successfully.", $logs_card);
    }
}
