<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            $response = Http::put(env('API_URL') . '/admin/admin/password-change', [
                'email' => $request->input('email'),
                'newPassword' => $request->input('new_password'),
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'Password changed successfully!']);
            } else {
                return response()->json(['message' => 'User not found!'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage());
            return response()->json(['message' => 'Server error: Unable to change password.'], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->input('email') !== session('email')) {
            return response()->json(['message' => 'Email does not match the logged in user.'], 403);
        }

        try {
            $response = Http::delete(env('API_URL') . '/admin/admin/delete-account', [
                'email' => $request->input('email'),
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'User account deleted successfully!']);
            } else {
                return response()->json(['message' => 'User not found!'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting account: ' . $e->getMessage());
            return response()->json(['message' => 'Server error: Unable to delete account.'], 500);
        }
    }
}
