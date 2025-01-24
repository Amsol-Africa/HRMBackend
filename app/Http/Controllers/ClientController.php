<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function fetch(Request $request)
    {
        $business_slug = session('active_business_slug');
        $business = Business::findBySlug($business_slug);

        $managed_businesses = $business->managedBusinesses;

        Log::debug($managed_businesses);

        $managed_businesse_cards = view('clients._clients_table', compact('managed_businesses'))->render();
        return RequestResponse::ok('Ok', $managed_businesse_cards);
    }
}
