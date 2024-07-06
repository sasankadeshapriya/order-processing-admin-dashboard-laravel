<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class VehicleController extends Controller
{
    private $baseURL = 'https://api.gsutil.xyz';
    //private $baseURL = ' http://127.0.0.1:4000';
   
   public function showData()
    {
        try {
            $response = Http::get("{$this->baseURL}/vehicle");
            $vehicles = $response->json();

            // Check if the response was successful (status code 2xx)
            if ($response->successful()) {
                $vehicles = $response->json();
                return view('pages.vehicle.vehicle', compact('vehicles'));
            } else {
                // Log the error
                \Log::error('API Error: ' . $response->status());

                // Return the error view with status code and message
                return view('pages.error')->with([
                    'errorCode' => $response->status(),
                ]);
            }
        } catch (RequestException $e) {
            // Log the error
            \Log::error('Request Exception: ' . $e->getMessage());

            // Get the HTTP status code from the response headers
            $statusCode = $e->response->status();

            // Return the error view with status code and message
            return view('pages.error')->with([
                'errorCode' => $statusCode,
                'errorMessage' => $e->response->json() // Assuming the API returns error messages in JSON format
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('General Exception: ' . $e->getMessage());

            // Return the error view with error code and description
            return view('pages.error')->with([
                'errorCode' => $e->getCode(),
                'errorMessage' => 'General Error: ' . $e->getMessage()
            ]);
        }
    }

    public function addVehicleForm()
    {
        return view('pages.vehicle.add-vehicle');
    }

    public function submitVehicle(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'vehicle_no' => 'required|string|max:8',
            'name' => 'required|string|min:2|max:50',
            'type' => 'required|string|in:Lorry,Van',
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $added_by_admin_id = (int) $request->input('added_by_admin_id');

            $vehicleData = [
                'vehicle_no' => $request->vehicle_no,
                'name' => $request->name,
                'type' => $request->type,
                'added_by_admin_id' => $added_by_admin_id,
            ];

            $vehicleResponse = Http::post("{$this->baseURL}/vehicle", $vehicleData);

            if ($vehicleResponse->successful()) {
                return response()->json(['success' => true, 'message' => 'Vehicle added successfully']);
            } else {
                // Capture and forward error message from the API
                return response()->json(['success' => false, 'message' => $vehicleResponse->json()['message'] ?? 'Failed to add vehicle']);
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add vehicle', 'errorDetail' => $e->getMessage()]);
        }
    }

    public function editVehicleForm($id)
    {
    $response = Http::get("{$this->baseURL}/vehicle/{$id}");

    if ($response->successful()) {
        $vehicle = $response->json();
        return view('pages.vehicle.edit-vehicle', ['vehicle' => (object) $vehicle]);
    } else {
        // Handle errors or redirect if the vehicle is not found
        return redirect()->route('vehicle.manage')->withErrors('Vehicle not found.');
    }
}

public function updateVehicle(Request $request, $id)
{
    try {
        \Log::info('Request Data:', $request->all());

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:8',
            'vehicle_name' => 'required|string|min:2|max:50',
            'vehicle_model' => 'required|in:Lorry,Van',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Map data from the request to the API's expected parameters
        $data = [
            'vehicle_no' => $request->input('vehicle_number'),
            'name' => $request->input('vehicle_name'),
            'type' => $request->input('vehicle_model'),
        ];

        \Log::info('Sending Data to API:', ['data' => $data]);

        // Send a PUT request to update the vehicle data
        $response = Http::put("{$this->baseURL}/vehicle/{$id}", $data);

        \Log::info('Response Status Code:', ['status' => $response->status()]);
        \Log::info('Response Body:', ['body' => $response->body()]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Vehicle successfully updated']);
        } else {
            \Log::error('Failed to update vehicle:', ['error' => $response->json()]);
            return response()->json(['success' => false, 'message' => $response->json('message')]);
        }
    } catch (\Exception $e) {
        \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['success' => false, 'message' => 'An unexpected error occurred']);
    }
}


public function deleteVehicle($id)
{
    try {
        $response = Http::delete("{$this->baseURL}/vehicle/$id");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete product']);
        }
    } catch (\Exception $e) {
        \Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: Unable to delete product']);
    }
}


}
