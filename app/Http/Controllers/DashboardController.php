<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        try {
            $response = Http::get('http://api.gsutil.xyz/dashboard/summary');

            // Check if the response was successful (status code 2xx)
            if ($response->successful()) {
                $data = $response->json();
                $totalAmount = $data['totalAmount'] ?? 0;
                $totalPaidAmount = $data['totalPaidAmount'] ?? 0;
                $routeCount = $data['routeCount'] ?? 0;
                $productCount = $data['productCount'] ?? 0;
                $soldProductDetails = $data['soldProductDetails'];

                return view('pages.home', compact('totalAmount', 'totalPaidAmount', 'routeCount', 'productCount', 'soldProductDetails'));
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
}
