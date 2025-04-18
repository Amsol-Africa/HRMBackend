<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;


Route::post('/external/applications', [ApplicationController::class, 'externalStore'])
    ->middleware('throttle:60,1')
    ->name('api.external.applications');

Route::post('/external/contact-submissions', [ContactSubmissionController::class, 'externalStore'])
    ->middleware('throttle:60,1')
    ->name('api.external.contact-submissions');
