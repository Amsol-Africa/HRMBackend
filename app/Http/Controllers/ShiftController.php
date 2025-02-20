<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Shift;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    use HandleTransactions;
    public function fetch(Request $request)
    {
        Log::debug(session()->all());
        $user = $request->user();
        $business = Business::findBySlug(session('active_business_slug'));

        $shifts = Shift::where('business_id', $business->id)->get();
        $shift_table = view('shifts._table', compact('shifts'))->render();
        return RequestResponse::ok('Ok', $shift_table);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shift_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $shift = $business->shifts()->create([
                'name' => $validatedData['shift_name'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'description' => $validatedData['description'] ?? null,
                'is_active' => true,
            ]);

            $shift->setStatus(Status::ACTIVE);

            return RequestResponse::created('Shift added successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'shift' => 'required|string|exists:shifts,slug',
        ]);

        $shift = Shift::findBySlug($validatedData['shift']);

        $shift_form = view('shifts._form', compact('shift'))->render();

        return RequestResponse::ok('Shift found', $shift_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'shift_slug' => 'required|exists:shifts,slug',
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {

            $shift = Shift::findBySlug($validatedData['shift_slug']);

            $shift->update([
                'name' => $validatedData['shift_name'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'description' => $validatedData['description'] ?? null,
            ]);

            $shift->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Shift updated successfully.');
        });
    }
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'shift' => 'required|exists:shifts,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $shift = Shift::where('slug', $validatedData['shift'])->first();

            if ($shift) {
                $shift->delete();
                return RequestResponse::ok('Shift deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete shift.', 404);
        });
    }

}