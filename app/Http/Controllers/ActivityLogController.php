<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\RequestResponse;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::latest()->paginate(20);
        $logs_card =view('components.activities', compact('logs'))->render();
        return RequestResponse::ok("Ok", $logs_card);
    }
}
