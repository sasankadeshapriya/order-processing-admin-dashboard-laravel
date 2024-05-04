<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MapController extends Controller
{
    public function submitRoute(Request $request)
    {
        Log::info('Received route submission request.', $request->all()); // Log all request data

        $validator = Validator::make($request->all(), [
            'route_name' => 'required|string|max:100',
            'waypoints' => 'required|json',
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            Log::error('Validation errors on route submission.', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Prepare data for the external API
        $apiUrl = 'https://api.gsutil.xyz/route'; // API endpoint
        $apiData = [
            'name' => $request->input('route_name'), // Change to 'name' to match API expectation
            'waypoints' => $request->input('waypoints'),
            'added_by_admin_id' => (int) $request->input('added_by_admin_id'), // Ensure it's an integer
        ];

        try {
            // Sending data to the Node.js API
            $response = Http::post($apiUrl, $apiData);

            if ($response->successful()) {
                Log::info('Map details added successfully via external API.', ['response' => $response->body()]);
                return response()->json(['success' => true, 'message' => 'Map details added successfully', 'data' => $response->json()]);
            } else {
                Log::error('Failed to add map details via external API.', ['response' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Failed to add map details', 'errorDetail' => $response->json()]);
            }
        } catch (\Exception $e) {
            Log::error('Server error while communicating with external API.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add map details', 'errorDetail' => $e->getMessage()]);
        }
    }

    public function showData()
    {
        $response = Http::get('http://api.gsutil.xyz/route');
        if ($response->successful()) {
            $routes = $response->json();

            foreach ($routes as &$route) {
                $waypoints = json_decode($route['waypoints'], true);

                // Debugging output
                Log::debug('Decoded waypoints:', $waypoints);

                if (!empty($waypoints) && is_array($waypoints)) {
                    $firstPoint = $waypoints[0];
                    $lastPoint = end($waypoints);

                    // Check if the array keys exist
                    if (isset($firstPoint['latitude'], $firstPoint['longitude'], $lastPoint['latitude'], $lastPoint['longitude'])) {
                        $route['waypoints_summary'] = "Start: ({$firstPoint['latitude']}, {$firstPoint['longitude']}) - End: ({$lastPoint['latitude']}, {$lastPoint['longitude']})";
                    } else {
                        $route['waypoints_summary'] = 'Missing latitude or longitude in waypoints.';
                    }
                } else {
                    $route['waypoints_summary'] = 'No waypoints defined';
                }
            }
            return view('pages.maps.manage', compact('routes'));
        } else {
            return view('pages.error')->with([
                'errorCode' => $response->status(),
                'errorMessage' => 'Failed to retrieve routes.'
            ]);
        }
    }


}
