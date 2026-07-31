<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Store Location
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::create([
            'user_name'  => $request->user_name,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'tracked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully.',
            'data' => $location,
        ], 201);
    }

    /**
     * Latest Location
     */
    public function latest()
    {
        $location = Location::latest()->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'No location found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $location,
        ]);
    }

    /**
     * Location History API
     */
    public function history()
    {
        $locations = Location::latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'count' => $locations->total(),
            'data' => $locations,
        ]);
    }

    /**
     * User Location History API
     */
    public function userHistory($user_name)
    {
        $locations = Location::where('user_name', $user_name)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'user' => $user_name,
            'total_locations' => $locations->count(),
            'data' => $locations,
        ]);
    }

    /**
     * Location Statistics API
     */
    public function statistics()
    {
        $latest = Location::latest()->first();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_locations' => Location::count(),
                'total_users' => Location::distinct('user_name')->count(),
                'latest_user' => optional($latest)->user_name,
                'last_updated' => optional($latest)->tracked_at,
                'latest_location' => $latest,
            ],
        ]);
    }

    /**
     * Delete All Location History
     */
    public function clearHistory()
    {
        Location::truncate();

        return response()->json([
            'success' => true,
            'message' => 'All location history deleted successfully.',
        ]);
    }

    /**
     * Nearby Locations API
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric'
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Radius in KM (default 5 KM)
        $radius = $request->radius ?? 5;

        $locations = Location::selectRaw("
            *,
            (
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude))
                )
            ) AS distance
        ", [
            $latitude,
            $longitude,
            $latitude
        ])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();

        return response()->json([
            'success' => true,
            'radius_km' => $radius,
            'total_locations' => $locations->count(),
            'data' => $locations
        ]);
    }

    /**
     * View
     */
    public function index()
    {
        return view('map');
    }
}
