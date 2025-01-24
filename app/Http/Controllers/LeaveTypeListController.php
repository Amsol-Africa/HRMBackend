<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveTypeList;

class LeaveTypeListController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = $request->get('query');
        $leaveTypes = LeaveTypeList::where('name', 'like', "%{$query}%")->pluck('name');
        return response()->json($leaveTypes);
    }
}
