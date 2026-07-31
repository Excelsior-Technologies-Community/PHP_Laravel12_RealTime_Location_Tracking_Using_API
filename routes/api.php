<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Location APIs
|--------------------------------------------------------------------------
*/

Route::post('/save-location', [LocationController::class, 'store']);

Route::get('/latest-location', [LocationController::class, 'latest']);

Route::get('/location-history', [LocationController::class, 'history']);

Route::get('/user-location/{user_name}', [LocationController::class, 'userHistory']);

Route::get('/location-stats', [LocationController::class, 'statistics']);

Route::get('/nearby-locations', [LocationController::class, 'nearby']);

Route::delete('/locations/clear', [LocationController::class, 'clearHistory']);