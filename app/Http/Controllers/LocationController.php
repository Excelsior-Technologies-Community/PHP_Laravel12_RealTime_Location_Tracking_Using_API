<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    // Store Location API
    public function store(Request $request)
    {
        $location = Location::create([
            'user_name' => $request->user_name,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Location Saved',
            'data' => $location
        ]);
    }

    // Get Latest Location API
    public function latest()
    {
        $location = Location::latest()->first();
        return response()->json($location);
    }

    // View Page
    public function index()
    {
        return view('map');
    }
}

