<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function showData()
    {
        try {
            $response = Http::get('https://api.gsutil.xyz/client');
            $clients = $response->json();

            if ($response->successful()) {
                return view('pages.client.client', compact('clients'));
            } else {
                Log::error('API Error: ' . $response->status());
                return view('pages.error')->with([
                    'errorCode' => $response->status(),
                ]);
            }
        } catch (RequestException $e) {
            Log::error('Request Exception: ' . $e->getMessage());
            return view('pages.error')->with([
                'errorCode' => $e->response->status(),
                'errorMessage' => $e->response->json()
            ]);
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            return view('pages.error')->with([
                'errorCode' => $e->getCode(),
                'errorMessage' => 'General Error: ' . $e->getMessage()
            ]);
        }
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
    
}
