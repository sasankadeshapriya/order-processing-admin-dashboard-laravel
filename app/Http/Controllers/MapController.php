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

        $validator = Validator::make($request->all(), [
            'route_name' => 'required|string|max:100',
            'waypoints' => 'required|json',
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $apiUrl = env('API_URL') . '/route'; // API endpoint
        $apiData = [
            'name' => $request->input('route_name'),
            'waypoints' => $request->input('waypoints'),
            'added_by_admin_id' => (int) $request->input('added_by_admin_id'),
        ];

        try {
            $response = Http::post($apiUrl, $apiData);

            if ($response->successful()) {
                Log::info('Map details added successfully via external API.', ['response' => $response->body()]);
                return response()->json(['success' => true, 'message' => 'Map details added successfully', 'data' => $response->json()]);
            } else {
                $responseBody = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $responseBody['message'] ?? 'Failed to add map details',
                    'errorDetail' => $responseBody
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Server error while communicating with external API.', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add map details', 'errorDetail' => $e->getMessage()]);
        }

    }

    public function showData()
    {
        $routes = [];

        try {
            $response = Http::get(env('API_URL') . '/route');

            if ($response->successful()) {
                $routes = $response->json();

                foreach ($routes as &$route) {
                    $waypoints = json_decode($route['waypoints'], true);

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
            } else {
                Log::error('API Error: ' . $response->status());
            }
        } catch (RequestException $e) {
            Log::error('Request Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
        }

        return view('pages.maps.manage', compact('routes'));
    }


    public function editRouteForm($id)
    {
        $response = Http::get(env('API_URL') . "/route/{$id}");

        if ($response->successful()) {
            $route = $response->json();
            return view('pages.maps.edit-route', ['route' => (object) $route]);
        } else {
            return redirect()->route('route.manage')->withErrors('Route not found.');
        }
    }

    public function updateRoute(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'route_name' => 'required|string|max:100',
            'waypoints' => 'required|json',
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $data = [
            'name' => $request->input('route_name'),
            'waypoints' => $request->input('waypoints'),
            'added_by_admin_id' => (int) $request->input('added_by_admin_id'),
        ];

        try {
            $response = Http::put(env('API_URL') . "/route/{$id}", $data);

            if ($response->successful()) {
                Log::info('Route updated successfully via external API.');
                return response()->json(['success' => true, 'message' => 'Route successfully updated']);
            } else {
                $errorDetails = $response->json();
                Log::error('Failed to update route:', ['response' => $errorDetails]);
                return response()->json(['success' => false, 'message' => $errorDetails['message'] ?? 'Failed to update route']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }


    public function deleteRoute($id)
    {
        try {
            $response = Http::delete(env('API_URL') . "/route/$id");

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Route deleted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete route']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: Unable to delete route']);
        }
    }

    public function showClientLocations()
    {
        try {
            $response = Http::get(env('API_URL') . '/client');

            if ($response->successful()) {
                $clients = $response->json();

                return response()->json(['success' => true, 'clients' => $clients]);
            } else {
                $errorDetails = $response->json();
                return response()->json(['success' => false, 'message' => 'Failed to retrieve client locations', 'errorDetail' => $errorDetails]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: Unable to fetch client locations', 'errorDetail' => $e->getMessage()]);
        }
    }


}
