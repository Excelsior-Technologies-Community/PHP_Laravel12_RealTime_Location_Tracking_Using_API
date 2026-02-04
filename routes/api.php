<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocationController;

Route::post('/save-location', [LocationController::class, 'store']);
Route::get('/latest-location', [LocationController::class, 'latest']);
