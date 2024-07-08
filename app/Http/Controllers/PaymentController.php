<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

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

public function toggleUpdateState($paymentId)
{
    try {
        $response = Http::put("https://api.gsutil.xyz/payment/{$paymentId}/state", [
            'state' => 'verified' // Example state to update
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['message' => 'Failed to update payment state'], $response->status());
        }
    } catch (RequestException $e) {
        Log::error('Request Exception: ' . $e->getMessage());
        return response()->json(['message' => 'API request failed'], $e->response->status());
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['message' => 'An unexpected error occurred'], 500);
    }
}
}
