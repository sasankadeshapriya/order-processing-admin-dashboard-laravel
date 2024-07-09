<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function showPayments()
{
    try {
        $responsePayments = Http::get('https://api.gsutil.xyz/payment');
        $responseInvoices = Http::get('https://api.gsutil.xyz/invoice');
        $responseClients = Http::get('https://api.gsutil.xyz/client');

        Log::info('Response from payment API', ['response' => $responsePayments->json()]);
        Log::info('Response from invoice API', ['response' => $responseInvoices->json()]);
        Log::info('Response from client API', ['response' => $responseClients->json()]);

        if ($responsePayments->successful() && $responseInvoices->successful() && $responseClients->successful()) {
            $payments = $responsePayments->json()['payments'];
            $invoices = $responseInvoices->json()['invoices'];
            $clients = $responseClients->json();

            // Create a mapping of client IDs to organization names
            $clientMap = [];
            foreach ($clients as $client) {
                $clientMap[$client['id']] = $client['organization_name'];
            }

            Log::info('Client Map', ['clientMap' => $clientMap]);

            // Filter payments to include only those with payment_option "cheque" and no deletedAt value
            $filteredPayments = array_filter($payments, function($payment) {
                $isCheque = $payment['payment_option'] === 'cheque';
                $isNotDeleted = is_null($payment['deletedAt']);
                Log::info('Filtering Payment', ['payment' => $payment, 'isCheque' => $isCheque, 'isNotDeleted' => $isNotDeleted]);
                return $isCheque && $isNotDeleted;
            });

            Log::info('Filtered Payments', ['filteredPayments' => $filteredPayments]);

            // Add organization names to filtered payments
            foreach ($filteredPayments as &$payment) {
                Log::info('Processing Payment', ['payment' => $payment]);
                $payment['organization_name'] = 'N/A'; // Default to 'N/A'

                foreach ($invoices as $invoice) {
                    Log::info('Processing Invoice', ['invoice' => $invoice]);

                    if ($payment['reference_number'] === $invoice['reference_number']) {
                        $payment['organization_name'] = $clientMap[$invoice['client_id']] ?? 'N/A';
                        break;
                    }
                }
            }

            Log::info('Processed payments with organization names', ['payments' => $filteredPayments]);

            return view('pages.payment.payment', ['payments' => $filteredPayments]);
        } else {
            Log::error('API Error: Unable to fetch payments, invoices, or clients.');
            return view('pages.error')->with(['errorCode' => 500, 'errorMessage' => 'Failed to fetch data from the server.']);
        }
    } catch (RequestException $e) {
        Log::error('Request Exception: ' . $e->getMessage());
        return view('pages.error')->with(['errorCode' => $e->response->status(), 'errorMessage' => 'API request failed.']);
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        return view('pages.error')->with(['errorCode' => $e->getCode(), 'errorMessage' => 'An unexpected error occurred.']);
    }
}

public function showAllPayments()
{
    try {
        $responsePayments = Http::get('https://api.gsutil.xyz/payment');
        $responseInvoices = Http::get('https://api.gsutil.xyz/invoice');
        $responseClients = Http::get('https://api.gsutil.xyz/client');

        Log::info('Response from payment API', ['response' => $responsePayments->json()]);
        Log::info('Response from invoice API', ['response' => $responseInvoices->json()]);
        Log::info('Response from client API', ['response' => $responseClients->json()]);

        if ($responsePayments->successful() && $responseInvoices->successful() && $responseClients->successful()) {
            $payments = $responsePayments->json()['payments'];
            $invoices = $responseInvoices->json()['invoices'];
            $clients = $responseClients->json();

            // Create a mapping of client IDs to organization names
            $clientMap = [];
            foreach ($clients as $client) {
                $clientMap[$client['id']] = $client['organization_name'];
            }

            Log::info('Client Map', ['clientMap' => $clientMap]);

            // Filter out payments that have a deletedAt value
            $filteredPayments = array_filter($payments, function($payment) {
                $isNotDeleted = is_null($payment['deletedAt']);
                Log::info('Filtering Payment', ['payment' => $payment, 'isNotDeleted' => $isNotDeleted]);
                return $isNotDeleted;
            });

            Log::info('Filtered Payments', ['filteredPayments' => $filteredPayments]);

            // Add organization names to filtered payments
            foreach ($filteredPayments as &$payment) {
                Log::info('Processing Payment', ['payment' => $payment]);
                $payment['organization_name'] = 'N/A'; // Default to 'N/A'

                foreach ($invoices as $invoice) {
                    Log::info('Processing Invoice', ['invoice' => $invoice]);

                    if ($payment['reference_number'] === $invoice['reference_number']) {
                        $payment['organization_name'] = $clientMap[$invoice['client_id']] ?? 'N/A';
                        break;
                    }
                }
            }

            Log::info('Processed payments with organization names', ['payments' => $filteredPayments]);

            return view('pages.payment.all-payments', ['payments' => $filteredPayments]);
        } else {
            Log::error('API Error: Unable to fetch payments, invoices, or clients.');
            return view('pages.error')->with(['errorCode' => 500, 'errorMessage' => 'Failed to fetch data from the server.']);
        }
    } catch (RequestException $e) {
        Log::error('Request Exception: ' . $e->getMessage());
        return view('pages.error')->with(['errorCode' => $e->response->status(), 'errorMessage' => 'API request failed.']);
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        return view('pages.error')->with(['errorCode' => $e->getCode(), 'errorMessage' => 'An unexpected error occurred.']);
    }
}


public function togglePaymentState(Request $request, $id)
    {
        Log::info('togglePaymentState called', ['id' => $id, 'state' => $request->state]);

        // Validate the request data
        $validator = \Validator::make($request->all(), [
            'state' => 'required|string|in:verified,not-verified',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }
        
        try {
            $validatedData = $validator->validated();
            $state = $validatedData['state'];

            Log::info('Updating payment state', ['id' => $id, 'state' => $state]);

            $url = "https://api.gsutil.xyz/payment/{$id}/state";
            Log::info("Making API request to URL: {$url} with state: {$state}");

            // Make the API request with the validated state
            $response = Http::put($url, [
                'state' => $state
            ]);

            if ($response->successful()) {
                Log::info('Payment state updated successfully', ['response' => $response->json()]);
                return response()->json(['success' => true, 'message' => 'Payment state updated successfully']);
            } else {
                Log::error('API Error', ['status' => $response->status(), 'response' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Failed to update payment state']);
            }
        } catch (\Exception $e) {
            Log::error('Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }   

public function deletePayment($id)
{
    try {
        $response = Http::delete("https://api.gsutil.xyz/payment/$id");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete payment']);
        }
    } catch (\Exception $e) {
        \Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: Unable to delete payment']);
    }
}
}
