<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    function index(Request $request)
    {
        $page = "Dashboard";
        return view('employee.index', compact('page'));
    }

}
