<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Validator;

class EmployeeController extends Controller
{
    public function showData()
    {
        try {
            $response = Http::get('https://api.gsutil.xyz/employee/all');
            $employees = $response->json()['employees'] ?? [];

            if ($response->successful()) {
                return view('pages.employee.employee', compact('employees'));
            } else {
                Log::error('API Error: ' . $response->status());
                // Return the employee view with empty employees array
                return view('pages.employee.employee', ['employees' => []]);
            }
        } catch (RequestException $e) {
            Log::error('Request Exception: ' . $e->getMessage());
            // Return the employee view with empty employees array
            return view('pages.employee.employee', ['employees' => []]);
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            // Return the employee view with empty employees array
            return view('pages.employee.employee', ['employees' => []]);
        }
    }

    public function addEmployeeForm()
    {
        return view('pages.employee.add-employee');
    }

    public function submitEmployee(Request $request)
    {
        \Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|min:2|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|string|min:8',
            'nic' => 'nullable|string',
            'phone_no' => 'nullable|string',
            'commission_rate' => 'nullable|numeric',
            'added_by_admin_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $employeeData = $request->only([
                'name', 'email', 'password', 'nic', 'phone_no', 'commission_rate', 'added_by_admin_id'
            ]);

            $employeeResponse = Http::post('https://api.gsutil.xyz/employee/add', $employeeData);

            if ($employeeResponse->successful()) {
                return response()->json(['success' => true, 'message' => 'Employee added successfully']);
            } else {
                return response()->json(['success' => false, 'message' => $employeeResponse->json()['message'] ?? 'Failed to add employee']);
            }
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: Unable to add employee', 'errorDetail' => $e->getMessage()]);
        }
    }

    public function editEmployeeForm($id)
    {
        $response = Http::get("https://api.gsutil.xyz/employee/{$id}/details");
    
        if ($response->successful()) {
            $employeeData = $response->json();
            if (isset($employeeData['employee'])) {
                $employee = (object) $employeeData['employee'];
                return view('pages.employee.edit-employee', ['employee' => $employee]);
            } else {
                return redirect()->route('employee.manage')->withErrors('Employee details not found.');
            }
        } else {
            return redirect()->route('employee.manage')->withErrors('Employee not found.');
        }
    }
    

    public function updateEmployee(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:50',
        'password' => 'nullable|string|min:8',
        'commission_rate' => 'nullable|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    $data = $request->except(['_token', '_method']);
    if ($request->filled('password')) {
        $data['password'] = bcrypt($request->password);
    } else {
        unset($data['password']);
    }

    Log::info('Data to be sent to API:', $data);

    $response = Http::put("https://api.gsutil.xyz/employee/{$id}", $data);
    if ($response->successful()) {
        Log::info('API Response:', $response->json());
        return response()->json(['success' => true, 'message' => 'Employee successfully updated']);
    } else {
        Log::error('API Update Failed:', $response->json());
        return response()->json(['success' => false, 'message' => 'Failed to update employee']);
    }
}

public function deleteEmployee($id)
{
    try {
        $response = Http::delete("https://api.gsutil.xyz/employee/$id");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete employee']);
        }
    } catch (\Exception $e) {
        \Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: Unable to delete employee']);
    }
}   


}
