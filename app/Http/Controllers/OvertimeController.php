<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Overtime;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OvertimeController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        $overtimes = Overtime::where('business_id', $business->id)
            ->with('employee.user', 'approvedBy')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        Log::debug('Fetched overtimes:', $overtimes->toArray());

        $overtimeTable = view('attendances._overtime_table', compact('overtimes'))->render();
        return RequestResponse::ok('Ok.', $overtimeTable);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'overtime_hours' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            return $this->handleTransaction(function () use ($validatedData) {
                $user = auth()->user();
                $business = Business::findBySlug(session('active_business_slug'));

                $overtime = Overtime::create([
                    'employee_id' => $validatedData['employee_id'],
                    'business_id' => $business->id,
                    'date' => $validatedData['date'],
                    'overtime_hours' => $validatedData['overtime_hours'],
                    'rate' => $this->getOvertimeRate($business),
                    'total_pay' => $validatedData['overtime_hours'] * $this->getOvertimeRate($business),
                    'description' => $validatedData['description'] ?? null, // Explicitly set to null if not provided
                    'approved_by' => $user->id,
                ]);

                $overtime->setStatus(Status::APPROVED);

                return RequestResponse::created('Overtime added and approved successfully.');
            });
        } catch (ValidationException $e) {
            return RequestResponse::badRequest('Validation failed.', ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('Failed to store overtime:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to add overtime.', [
                'errors' => ['An error occurred: ' . $e->getMessage()]
            ]);
        }
    }

    private function getOvertimeRate($business)
    {
        return $business->overtime_rate ?? 1.5;
    }

    public function destroy(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'overtime' => 'required|exists:overtimes,id',
            ]);

            return $this->handleTransaction(function () use ($validatedData) {
                $business = Business::findBySlug(session('active_business_slug'));
                $overtime = Overtime::where('business_id', $business->id)
                    ->where('id', $validatedData['overtime'])
                    ->firstOrFail();

                $overtime->delete();

                return RequestResponse::ok('Overtime deleted successfully.');
            });
        } catch (ValidationException $e) {
            return RequestResponse::badRequest('Validation failed.', ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('Failed to delete overtime:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to delete overtime.', [
                'errors' => ['An error occurred: ' . $e->getMessage()]
            ]);
        }
    }
}