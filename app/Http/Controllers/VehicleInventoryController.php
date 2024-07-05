<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class VehicleInventoryController extends Controller
{

    public function showVehicleInventory()
    {
        try {
            $response = Http::get('https://api.gsutil.xyz/vehicle-inventory');
            $vehicleInventories = collect($response->json('vehicleInventories'));

            // Group by Vehicle No.
            $groupedInventories = $vehicleInventories->groupBy([
                function ($item) {
                    return $item['Assignment']['Vehicle']['vehicle_no']; // Grouping by vehicle number
                },
                function ($item) {
                    return substr($item['Assignment']['assign_date'], 0, 10); // Further grouping by assign date
                }
            ]);

            return view('pages.vehicle-inventory.vehicle-inventory', compact('groupedInventories'));
        } catch (\Throwable $e) {
            \Log::error('Error: ' . $e->getMessage());
            return view('pages.error')->with([
                'errorCode' => $e->getCode(),
                'errorMessage' => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $response = Http::delete("https://api.gsutil.xyz/vehicle-inventory/$id");

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

    public function addVehicleInventoryForm()
    {
        try {
            $batches = Http::get('https://api.gsutil.xyz/batch')->json();
            $products = collect($batches)->map(function ($batch) {
                return [
                    'id' => $batch['product_id'],
                    'name' => $batch['Product']['name'],
                    'sku' => $batch['sku'],
                    'quantity' => $batch['quantity']
                ];
            })->unique('id');
        } catch (\Exception $e) {
            \Log::error('Error fetching batches: ' . $e->getMessage());
            $products = [];
        }

        return view('pages.vehicle-inventory.add-vehicle-inventory', compact('products'));
    }

    public function submitVehicleInventory(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $request->validate([
            'assignment_id' => 'required|integer',
            'product_id' => 'required|integer',
            'sku' => 'required|string',
            'quantity' => 'required|numeric|min:0.5',
            'added_by_admin_id' => 'required|integer'
        ]);

        $data = [
            'assignment_id' => (int) $request->input('assignment_id'),
            'product_id' => (int) $request->input('product_id'),
            'sku' => $request->input('sku'),
            'quantity' => (float) $request->input('quantity'),
            'added_by_admin_id' => (int) $request->input('added_by_admin_id')
        ];

        try {
            \Log::info('Final Data:', $data);

            $response = Http::post('http://api.gsutil.xyz/vehicle-inventory', $data);

            if ($response->successful()) {
                // Fetch updated batches to return
                $updatedBatches = Http::get('https://api.gsutil.xyz/batch')->json();
                $products = collect($updatedBatches)->map(function ($batch) {
                    return [
                        'id' => $batch['product_id'],
                        'name' => $batch['Product']['name'],
                        'sku' => $batch['sku'],
                        'quantity' => $batch['quantity']
                    ];
                })->unique('id')->values()->all();

                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle inventory added successfully.',
                    'products' => $products
                ]);
            } else {
                return response()->json(['success' => false, 'message' => $response->json()['message'] ?? 'Failed to add Vehicle inventory']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add Vehicle inventory', 'errorDetail' => $e->getMessage()]);
        }
    }

    public function editVehicleInventoryForm($id)
    {
        try {
            // Fetching specific inventory data
            $response = Http::get("http://api.gsutil.xyz/vehicle-inventory/{$id}");
            $responseData = $response->json();

            // Logging for debugging
            \Log::info('API Response:', $responseData);

            // Check if the response is successful and has the necessary data
            if (!$response->successful() || !isset($responseData['vehicleInventory'])) {
                \Log::error('No inventory data found in the API response.');
                return redirect()->route('vehicle.inventory')->with('error', 'No inventory data found.');
            }

            $inventory = $responseData['vehicleInventory'];

            // Fetching batch data for products from the API
            $batches = Http::get('https://api.gsutil.xyz/batch')->json();
            $products = collect($batches)->map(function ($batch) {
                return [
                    'id' => $batch['product_id'],
                    'name' => $batch['Product']['name'],
                    'sku' => $batch['sku'],
                    'quantity' => $batch['quantity']
                ];
            })->unique('id');

        } catch (\Exception $e) {
            \Log::error('Error fetching data: ' . $e->getMessage());
            return redirect()->route('vehicle.inventory')->with('error', 'Failed to fetch data for the specified inventory');
        }

        return view('pages.vehicle-inventory.edit-vehicle-inventory', compact('inventory', 'products'));
    }

    public function updateVehicleInventory(Request $request, $id)
    {
        \Log::info('Request Data:', $request->all());

        $request->validate([
            'assignment_id' => 'required|integer',
            'product_id' => 'required|integer',
            'sku' => 'required|string',
            'quantity' => 'required|numeric|min:0.5',
            'added_by_admin_id' => 'required|integer'
        ]);

        $data = [
            'assignment_id' => (int) $request->input('assignment_id'),
            'product_id' => (int) $request->input('product_id'),
            'sku' => $request->input('sku'),
            'quantity' => (float) $request->input('quantity'),
            'added_by_admin_id' => (int) $request->input('added_by_admin_id'),
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->put("http://api.gsutil.xyz/vehicle-inventory/admin/{$id}", $data);

            if ($response->successful()) {
                $responseBody = $response->json();

                // Fetch the latest batch data for the specific product to compute max quantity correctly
                $updatedBatches = Http::get('https://api.gsutil.xyz/batch')->json();
                $updatedProduct = collect($updatedBatches)->firstWhere('product_id', $data['product_id']);

                // Calculate the maximum available quantity: batch available quantity + current inventory quantity
                $currentInventoryQuantity = $data['quantity'];
                $batchAvailableQuantity = isset($updatedProduct['quantity']) ? $updatedProduct['quantity'] : 0;
                $maxQuantity = $currentInventoryQuantity + $batchAvailableQuantity;

                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle inventory updated successfully.',
                    'maxQuantity' => $maxQuantity,  // Total of current in inventory and what's available in the batch
                    'availableQuantity' => $batchAvailableQuantity  // Just the batch available quantity
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Failed to update vehicle inventory'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Server error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: Unable to update vehicle inventory',
                'errorDetail' => $e->getMessage()
            ]);
        }
    }

}
