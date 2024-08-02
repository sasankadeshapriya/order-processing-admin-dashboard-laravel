<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class BatchController extends Controller
{

    public function showData()
    {
        try {
            $response = Http::get(env('API_URL') . '/batch');
            $items = $response->json();

            // Check if the response was successful (status code 2xx)
            if ($response->successful()) {
                $items = $response->json();
                return view('pages.batch.batch', compact('items'));
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

    public function deleteBatch($id)
    {
        try {
            $response = Http::delete(env('API_URL') . "/batch/$id");

            if ($response->successful()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete batch']);
            }
        } catch (\Exception $e) {
            \Log::error('General Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to delete batch']);
        }
    }

    public function addBatchForm()
    {
        try {
            $response = Http::get(env('API_URL') . '/product');

            if ($response->successful()) {
                $products = $response->json();
            } else {
                // Handle API error response
                $products = [];
            }
        } catch (\Exception $e) {
            // Handle exception
            \Log::error('Error fetching products: ' . $e->getMessage());
            $products = [];
        }
        // Pass the products data to the view
        return view('pages.batch.add-batch', compact('products'));
    }


    public function submitBatch(Request $request)
    {
        try {
            // Log the incoming request data
            \Log::info('Request Data:', $request->all());

            // Collecting and formatting batch data from the request
            $batchData = [
                'sku' => $request->input('sku'),
                'product_id' => (int) $request->input('product_id'), // Cast to integer
                'buy_price' => (float) $request->input('buy_price'), // Cast to float
                'cash_price' => (float) $request->input('cash_price'), // Cast to float
                'check_price' => (float) $request->input('check_price'), // Cast to float
                'credit_price' => (float) $request->input('credit_price'), // Cast to float
                'quantity' => (float) $request->input('quantity'), // Cast to integer
                'expire_date' => $request->input('expire_date'), // Direct input assuming format is correct
                'added_by_admin_id' => (int) $request->input('added_by_admin_id') // Cast to integer
            ];

            // Log the final batch data
            \Log::info('Final Data:', $batchData);

            // Validate the data
            $validator = Validator::make($batchData, [
                'sku' => 'required|string',
                'product_id' => 'required|integer|min:1',
                'buy_price' => 'required|numeric|min:0.01',
                'cash_price' => 'required|numeric|min:0.01',
                'check_price' => 'required|numeric|min:0.01',
                'credit_price' => 'required|numeric|min:0.01',
                'quantity' => 'required|numeric|min:0.5',
                'expire_date' => 'required|date_format:Y-m-d',
                'added_by_admin_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }

            // Sending the data to the API
            $response = Http::post(env('API_URL') . '/batch', $batchData);

            // Handle API response
            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Batch added successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to add batch']);
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add batch', 'errorDetail' => $e->getMessage()]);
        }
    }


    public function editBatchForm($id)
    {
        $batchResponse = Http::get(env('API_URL') . "/batch/{$id}");
        if (!$batchResponse->successful()) {
            return redirect()->route('batch.manage')->withErrors('Batch not found.');
        }
        $batch = $batchResponse->object(); // Convert to object

        // Debugging API response for products
        $productsResponse = Http::get(env('API_URL') . '/product');
        if (!$productsResponse->successful()) {
            \Log::error("Failed to fetch products: " . $productsResponse->body());
            $products = [];
        } else {
            $products = $productsResponse->json(); // Keep as array or convert to objects
            \Log::info("Fetched products: ", $products); // Debugging products fetched
        }

        return view('pages.batch.edit-batch', [
            'batch' => $batch,
            'products' => $products
        ]);
    }


    public function updateBatch(Request $request, $id)
    {
        // Create a formatted array from the request with explicit type casting
        $data = [
            'sku' => $request->input('sku'),
            'product_id' => (int) $request->input('product_id'), // Cast to integer
            'buy_price' => (float) $request->input('buy_price'), // Cast to float
            'cash_price' => (float) $request->input('cash_price'), // Cast to float
            'check_price' => (float) $request->input('check_price'), // Cast to float
            'credit_price' => (float) $request->input('credit_price'), // Cast to float
            'quantity' => (float) $request->input('quantity'), // Cast to float
            'expire_date' => $request->input('expire_date') // Assume valid date format
        ];

        \Log::info('Final Data:', $data);

        // Validate the formatted data
        $validator = Validator::make($data, [
            'sku' => 'required|string|max:255',
            'product_id' => 'required|integer',
            'buy_price' => 'required|numeric|min:0.01',
            'cash_price' => 'required|numeric|min:0.01',
            'check_price' => 'required|numeric|min:0.01',
            'credit_price' => 'required|numeric|min:0.01',
            'quantity' => 'required|numeric|min:0.5',
            'expire_date' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // Sending the formatted data to the API
        $response = Http::put(env('API_URL') . "/batch/{$id}", $data);
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Batch successfully updated']);
        } else {
            // Get the error message from the API response
            $errorMessage = $response->json()['message'] ?? 'Failed to update batch';
            return response()->json(['success' => false, 'message' => $errorMessage]);
        }
    }

}
