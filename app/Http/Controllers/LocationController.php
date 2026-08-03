<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Location History
     */
    public function history()
    {
        $locations = Location::oldest()->get();

        return response()->json([
            'success' => true,
            'count' => $locations->count(),
            'data' => $locations,
        ]);
    }

    /**
     * User History
     */
    public function userHistory($user_name)
    {
        $locations = Location::where('user_name', $user_name)
            ->oldest()
            ->get();

        return response()->json([
            'success' => true,
            'user' => $user_name,
            'total_locations' => $locations->count(),
            'data' => $locations,
        ]);
    }

    /**
     * Statistics
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
     * Clear History
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
     * Nearby Locations
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
            'data' => $locations,
        ]);
    }

    /**
     * Search by User Name
     */
    public function search(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
        ]);

        $locations = Location::where('user_name', 'LIKE', '%' . $request->user_name . '%')
            ->oldest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'search' => $request->user_name,
            'count' => $locations->total(),
            'data' => $locations,
        ]);
    }

    /**
     * Filter by Date
     */
    public function filter(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $locations = Location::whereDate('tracked_at', $request->date)
            ->oldest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'date' => $request->date,
            'count' => $locations->total(),
            'data' => $locations,
        ]);
    }

    /**
     * Delete Single Location
     */
    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found.',
            ], 404);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully.',
        ]);
    }

    /**
     * Export CSV
     */
    public function exportCsv()
    {
        $fileName = 'locations.csv';

        $locations = Location::latest()->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($locations) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'User Name',
                'Latitude',
                'Longitude',
                'Tracked At',
            ]);

            foreach ($locations as $location) {
                fputcsv($file, [
                    $location->id,
                    $location->user_name,
                    $location->latitude,
                    $location->longitude,
                    $location->tracked_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Dashboard View
     */
    public function index()
    {
        return view('map');
    }
}
