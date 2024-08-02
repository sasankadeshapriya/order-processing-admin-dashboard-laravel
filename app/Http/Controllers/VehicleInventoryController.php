<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class VehicleInventoryController extends Controller
{


    public function showVehicleInventory()
    {
        try {
            $response = Http::get(env('API_URL') . '/vehicle-inventory');
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
            $response = Http::delete(env('API_URL') . "/vehicle-inventory/$id");

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

    private $baseURL;
    public function __construct()
    {
        $this->baseURL = env('API_URL');
    }

    public function addVehicleInventoryForm()
    {
        try {
            $batches = Http::get("{$this->baseURL}/batch")->json();
            $assignmentsResponse = Http::get("{$this->baseURL}/assignment");
            $employeesResponse = Http::get("{$this->baseURL}/employee/all");
            $vehiclesResponse = Http::get("{$this->baseURL}/vehicle");
            $routesResponse = Http::get("{$this->baseURL}/route");

            if ($assignmentsResponse->successful() && $employeesResponse->successful() && $vehiclesResponse->successful() && $routesResponse->successful()) {
                $assignments = $assignmentsResponse->json();
                $employees = $employeesResponse->json()['employees'] ?? [];
                $vehicles = $vehiclesResponse->json();
                $routes = $routesResponse->json();

                $employeeMap = collect($employees)->pluck('name', 'id');
                $vehicleMap = collect($vehicles)->pluck('vehicle_no', 'id');
                $routeMap = collect($routes)->pluck('name', 'id');

                foreach ($assignments as &$assignment) {
                    $assignment['employee_name'] = $employeeMap[$assignment['employee_id']] ?? 'Unknown';
                    $assignment['vehicle_number'] = $vehicleMap[$assignment['vehicle_id']] ?? 'Unknown';
                    $assignment['route_name'] = $routeMap[$assignment['route_id']] ?? 'Unknown';
                    $assignment['assign_date'] = Carbon::parse($assignment['assign_date'])->toDateString();
                }

                $products = collect($batches)->map(function ($batch) {
                    return [
                        'id' => $batch['product_id'],
                        'name' => $batch['Product']['name'],
                        'sku' => $batch['sku'],
                        'quantity' => $batch['quantity']
                    ];
                })->unique('id');

                return view('pages.vehicle-inventory.add-vehicle-inventory', compact('products', 'assignments'));
            } else {
                return redirect('/error');
            }
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return redirect('/error');
        }
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

            $response = Http::post(env('API_URL') . '/vehicle-inventory', $data);

            if ($response->successful()) {
                // Fetch updated batches to return
                $updatedBatches = Http::get(env('API_URL') . '/batch')->json();
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
            $response = Http::get(env('API_URL') . "/vehicle-inventory/{$id}");
            $responseData = $response->json();

            // Check if the response is successful and has the necessary data
            if (!$response->successful() || !isset($responseData['vehicleInventory'])) {
                return redirect()->route('vehicle.inventory')->with('error', 'No inventory data found.');
            }

            $inventory = $responseData['vehicleInventory'];

            // Fetching batch data for products from the API
            $batches = Http::get(env('API_URL') . '/batch')->json();
            $products = collect($batches)->map(function ($batch) {
                return [
                    'id' => $batch['product_id'],
                    'name' => $batch['Product']['name'],
                    'sku' => $batch['sku'],
                    'quantity' => $batch['quantity']
                ];
            })->unique('id');

        } catch (\Exception $e) {
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
            ])->put(env('API_URL') . "/vehicle-inventory/admin/{$id}", $data);

            if ($response->successful()) {
                $responseBody = $response->json();

                // Fetch the latest batch data for the specific product to compute max quantity correctly
                $updatedBatches = Http::get(env('API_URL') . '/batch')->json();
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
