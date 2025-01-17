<?php

namespace App\Traits;

use App\Http\RequestResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait HandleTransactions
{
    public function handleTransaction(\Closure $callback)
    {
        DB::beginTransaction();
        try {
            $response = $callback();
            DB::commit();
            return $response;
        } catch (ValidationException $e) {
            DB::rollBack();
            $this->logError($e);
            return RequestResponse::badRequest('Validation failed.', $e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return RequestResponse::internalServerError($e->getMessage());
        }
    }

    private function logError(\Exception $e)
    {
        report($e);
        Log::error($e->getCode() . " " . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
    }
}
