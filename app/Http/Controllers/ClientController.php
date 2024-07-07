<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    //private $baseURL = ' http://127.0.0.1:4000/client';
    
    public function showData()
    {
        $clients = [];
        try {
            $response = Http::get('https://api.gsutil.xyz/client');
            if ($response->successful()) {
                $clients = $response->json();
            } else {
                Log::error('API Error: ' . $response->status());
            }
        } catch (RequestException $e) {
            Log::error('Request Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
        }
    
        return view('pages.client.client', compact('clients'));
    }
    
    

    public function addClientForm()
    {
        try {
            // Fetch routes from the API
            $response = Http::get('https://api.gsutil.xyz/route');
    
            if ($response->successful()) {
                $routes = $response->json();
            } else {
                $routes = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching routes: ' . $e->getMessage());
            $routes = [];
        }
    
        return view('pages.client.add-client', compact('routes'));
    }
    
    public function submitClient(Request $request)
    {
        \Log::info('Request Data:', $request->all());
    
        // Define validation rules with specific data types and formats
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'organization_name' => 'required|string|min:2|max:100',
            'phone_no' => 'required|string|max:15|regex:/^\+?\d{9,13}$/',
            'status' => 'nullable|string|in:verified,not verified',
            'discount' => 'required|numeric|between:0,100',
            'credit_limit' => 'required|numeric|min:0',
            'credit_period' => 'required|integer|min:1',
            'route_id' => 'required|integer',
            'added_by_employee_id' => 'required|integer',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation Errors: ', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }
    
        try {
            $validatedData = $validator->validated();
            $clientResponse = Http::post('https://api.gsutil.xyz/client', $validatedData);
    
            if ($clientResponse->successful()) {
                return response()->json(['success' => true, 'message' => 'Client added successfully']);
            } else {
                return response()->json(['success' => false, 'message' => $clientResponse->json()['message'] ?? 'Failed to add client']);
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add client', 'errorDetail' => $e->getMessage()]);
        }
    } 
    
    public function editClientForm($id)
{
    try {
        // Fetch the client data
        $clientResponse = Http::get("https://api.gsutil.xyz/client/{$id}");
        
        if ($clientResponse->successful()) {
            $client = $clientResponse->json();
        } else {
            return redirect()->route('client.manage')->withErrors('Client not found.');
        }
        
        // Fetch the routes data
        $routesResponse = Http::get('https://api.gsutil.xyz/route'); // Adjust the endpoint as necessary
        
        if ($routesResponse->successful()) {
            $routes = $routesResponse->json();
            Log::info('Routes data fetched successfully:', $routes);
        } else {
            Log::error('Error fetching routes: ' . $routesResponse->status());
            $routes = [];
        }

        return view('pages.client.edit-client', ['client' => (object) $client, 'routes' => $routes]);
    } catch (\Exception $e) {
        Log::error('Exception in editClientForm: ' . $e->getMessage());
        return redirect()->route('client.manage')->withErrors('An error occurred while fetching client data.');
    }
}


    

public function updateClient(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:2|max:50',
        'organization_name' => 'nullable|string|max:100',
        'phone_no' => 'required|string|min:10|max:15',
        'status' => 'required|in:verified,not verified',
        'discount' => 'nullable|numeric|min:0|max:100',
        'credit_limit' => 'nullable|numeric|min:0',
        'credit_period' => 'nullable|numeric|min:1',
        'route_id' => 'required|numeric',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'added_by_employee_id' => 'required|numeric'
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    $data = $request->except(['_token', '_method']);
    
    $response = Http::put("https://api.gsutil.xyz/client/{$id}", $data);

    if ($response->successful()) {
        return response()->json(['success' => true, 'message' => 'Client successfully updated']);
    } else {
        return response()->json(['success' => false, 'message' => 'Failed to update client']);
    }
}

// Method to toggle client status
public function toggleClientStatus(Request $request, $id)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'status' => 'required|string|in:verified,not verified',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    try {
        $validatedData = $validator->validated();
        $status = $validatedData['status'];

        // Make the API request with the validated status
        $response = Http::post("https://api.gsutil.xyz/client/verify/{$id}", [
            'status' => $status
        ]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Client status updated successfully']);
        } else {
            Log::error('API Error: ' . $response->status());
            return response()->json(['success' => false, 'message' => 'Failed to update client status']);
        }
    } catch (RequestException $e) {
        Log::error('Request Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Request error: ' . $e->getMessage()]);
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
}

public function deleteClient($id)
    {
        try {
            $response = Http::delete("https://api.gsutil.xyz/client/$id");

            if ($response->successful()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete client']);
            }
        } catch (\Exception $e) {
            \Log::error('General Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to delete client']);
        }
    }
    
}
