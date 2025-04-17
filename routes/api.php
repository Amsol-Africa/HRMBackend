<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;


Route::post('/external/applications', [ApplicationController::class, 'externalStore'])
    ->middleware('throttle:60,1')
    ->name('api.external.applications');
