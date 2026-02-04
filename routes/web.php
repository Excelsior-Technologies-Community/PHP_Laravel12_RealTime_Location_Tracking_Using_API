<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LocationController;

Route::get('/', [LocationController::class, 'index']);
