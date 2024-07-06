<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    private $baseURL = 'https://api.gsutil.xyz';
    //private $baseURL = ' http://127.0.0.1:4000';
    public function showAssignments()
{
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
            $assignment['assign_date'] = Carbon::parse($assignment['assign_date'])->toDateString(); // Format the date
        }

        return view('pages.assignments.manage-assignments', compact('assignments'));
    } else {
        return back()->withErrors('Failed to fetch data from external APIs.');
    }
}

public function addAssignmentForm()
{
    // Fetch data from external API
    $employeesResponse = Http::get("{$this->baseURL}/employee/all");
    $vehiclesResponse = Http::get("{$this->baseURL}/vehicle");
    $routesResponse = Http::get("{$this->baseURL}/route");

    $employees = $employeesResponse->successful() ? $employeesResponse->json()['employees'] : [];
    $vehicles = $vehiclesResponse->successful() ? $vehiclesResponse->json() : [];
    $routes = $routesResponse->successful() ? $routesResponse->json() : [];

    return view('pages.assignments.add-assignment', compact('employees', 'vehicles', 'routes'));
}
public function submitAssignment(Request $request)
{
    // Log all request data
    \Log::info('Request Data:', $request->all());

    // Manually convert string data to integers
    $request->merge([
        'employee_id' => intval($request->employee_id),
        'vehicle_id' => intval($request->vehicle_id),
        'route_id' => intval($request->route_id)
    ]);

    // Validation rules setup
    $validator = Validator::make($request->all(), [
        'employee_id' => 'required|integer',
        'vehicle_id' => 'required|integer',
        'route_id' => 'required|integer',
        'assign_date' => 'required|date',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        \Log::info('Validation Errors:', $validator->errors()->toArray());
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    try {
        // Preparing data to send
        $data = $request->only('employee_id', 'vehicle_id', 'route_id', 'assign_date');
        $data['added_by_admin_id'] = 1; // Static admin ID example

        // Sending data to external API
        $response = Http::post("{$this->baseURL}/assignment", $data);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Assignment added successfully']);
        } else {
            // Handling API errors
            return response()->json(['success' => false, 'message' => $response->json()['message'] ?? 'Failed to add assignment']);
        }
    } catch (\Exception $e) {
        \Log::error('Error: ' . $e->getMessage());
        // Handling server side exceptions
        return response()->json(['success' => false, 'message' => 'Server error: Unable to add assignment', 'errorDetail' => $e->getMessage()]);
    }
}

public function editAssignmentForm($id)
{
    $assignmentResponse = Http::get("{$this->baseURL}/assignment/{$id}");
    $employeesResponse = Http::get("{$this->baseURL}/employee/all");
    $vehiclesResponse = Http::get("{$this->baseURL}/vehicle");
    $routesResponse = Http::get("{$this->baseURL}/route");

    if ($assignmentResponse->successful()) {
        $assignment = $assignmentResponse->json();
        $assignment['assign_date'] = \Carbon\Carbon::parse($assignment['assign_date'])->format('Y-m-d'); // Convert date format

        $employees = $employeesResponse->successful() ? $employeesResponse->json()['employees'] : [];
        $vehicles = $vehiclesResponse->successful() ? $vehiclesResponse->json() : [];
        $routes = $routesResponse->successful() ? $routesResponse->json() : [];

        Log::info('Assignment:', $assignment);
        Log::info('Employees:', $employees);
        Log::info('Vehicles:', $vehicles);
        Log::info('Routes:', $routes);

        return view('pages.assignments.edit-assignment', compact('assignment', 'employees', 'vehicles', 'routes'));
    } else {
        return redirect()->route('assignment.manage')->withErrors('Assignment not found.');
    }
}


public function updateAssignment(Request $request, $id)
{
    \Log::info('Request Data:', $request->all());

    // Convert data types as needed
    $data = [
        'employee_id' => (int) $request->input('employee_id'),
        'vehicle_id' => (int) $request->input('vehicle_id'),
        'route_id' => (int) $request->input('route_id'),
        'assign_date' => $request->input('assign_date'),
        'added_by_admin_id' => (int) $request->input('added_by_admin_id', 1) // Use default if not present
    ];

    // Define validation rules
    $validator = Validator::make($data, [
        'employee_id' => 'required|integer',
        'vehicle_id' => 'required|integer',
        'route_id' => 'required|integer',
        'assign_date' => 'required|date',
        'added_by_admin_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    $response = Http::put("{$this->baseURL}/assignment/{$id}", $data);

    if ($response->successful()) {
        return response()->json(['success' => true, 'message' => 'Assignment successfully updated']);
    } else {
        // Check if 'errors' key exists in the response
        $errorResponse = $response->json();
        $errors = isset($errorResponse['errors']) ? $errorResponse['errors'] : 'Unknown error';
        $message = isset($errorResponse['message']) ? $errorResponse['message'] : 'Failed to update assignment';

        \Log::error('Failed to update assignment:', ['response' => $errorResponse]);
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors]);
    }
}

public function deleteAssignment($id)
{
    try {
        $response = Http::delete("{$this->baseURL}/assignment/$id");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete assignment']);
        }
    } catch (\Exception $e) {
        \Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: Unable to delete assignment']);
    }
}

public function showTodayAssignments()
{
    $today = now()->toDateString();

    $assignmentsResponse = Http::get("{$this->baseURL}/assignment");
    $employeesResponse = Http::get("{$this->baseURL}/employee/all");
    $vehiclesResponse = Http::get("{$this->baseURL}/vehicle");
    $routesResponse = Http::get("{$this->baseURL}/route");

    // Log the responses for debugging.
    Log::info('Assignments Response:', $assignmentsResponse->json());
    Log::info('Employees Response:', $employeesResponse->json());
    Log::info('Vehicles Response:', $vehiclesResponse->json());
    Log::info('Routes Response:', $routesResponse->json());

    if ($assignmentsResponse->successful() && $employeesResponse->successful() &&
        $vehiclesResponse->successful() && $routesResponse->successful()) {

        $assignments = array_filter($assignmentsResponse->json(), function ($assignment) use ($today) {
            return now()->parse($assignment['assign_date'])->toDateString() === $today;
        });

        $employees = collect($employeesResponse->json()['employees'] ?? []);
        $vehicles = collect($vehiclesResponse->json());
        $routes = collect($routesResponse->json());

        $employeeMap = $employees->pluck('name', 'id');
        $vehicleMap = $vehicles->pluck('vehicle_no', 'id');
        $routeMap = $routes->pluck('name', 'id');
        $waypointMap = $routes->pluck('waypoints', 'id');

        foreach ($assignments as &$assignment) {
            $assignment['employee_name'] = $employeeMap->get($assignment['employee_id'], 'Unknown');
            $assignment['vehicle_number'] = $vehicleMap->get($assignment['vehicle_id'], 'Unknown');
            $assignment['route_name'] = $routeMap->get($assignment['route_id'], 'Unknown');
            $assignment['assign_date'] = now()->parse($assignment['assign_date'])->toDateString();
            $assignment['waypoints'] = json_decode($waypointMap->get($assignment['route_id'], '[]'), true);
        }

        return view('pages.assignments.emp-tracking', compact('assignments'));

    } else {
        return back()->withErrors('Failed to fetch data from external APIs.');
    }
}

public function getEmployeeLocation($employeeId)
{
    try {
        $response = Http::get("{$this->baseURL}/employee/{$employeeId}/location");

        if ($response->successful()) {
            $locationData = $response->json()['location'];
            return response()->json(['location' => $locationData]);
        } else {
            return response()->json(['message' => 'Failed to retrieve employee location'], 500);
        }
    } catch (\Exception $e) {
        Log::error("Error retrieving employee location for ID {$employeeId}: {$e->getMessage()}");
        return response()->json(['message' => 'Failed to retrieve employee location'], 500);
    }
}

public function getClientsByRoute($routeId)
{
    try {
        $response = Http::get("{$this->baseURL}/client/route/{$routeId}");

        if ($response->successful()) {
            $clientsData = $response->json();
            return response()->json($clientsData);
        } else {
            return response()->json(['message' => 'Failed to retrieve clients for route'], 500);
        }
    } catch (\Exception $e) {
        Log::error("Error retrieving clients for route ID {$routeId}: {$e->getMessage()}");
        return response()->json(['message' => 'Failed to retrieve clients for route'], 500);
    }
}
}

