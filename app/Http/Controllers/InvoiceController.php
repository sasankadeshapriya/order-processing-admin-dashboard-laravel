<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function showInvoices()
{
    try {
        // Fetch the invoices
        $response = Http::get('https://api.gsutil.xyz/invoice');
        $invoices = collect($response->json('invoices'));

        // Fetch related data
        $clientsResponse = Http::get('https://api.gsutil.xyz/client');
        $clients = collect($clientsResponse->json());

        $employeesResponse = Http::get('https://api.gsutil.xyz/employee/all');
        $employees = collect($employeesResponse->json('employees'));

        $productsResponse = Http::get('https://api.gsutil.xyz/product');
        $products = collect($productsResponse->json());

        // Log the products fetched
        \Log::info('Fetched products:', $products->toArray());

        // Add client, employee, and product data to each invoice
        $invoices->transform(function ($invoice) use ($clients, $employees, $products) {
            $invoice['client'] = $clients->firstWhere('id', $invoice['client_id']);
            $invoice['employee'] = $employees->firstWhere('id', $invoice['employee_id']);
            if (isset($invoice['InvoiceDetails'])) {
                $invoice['products'] = collect($invoice['InvoiceDetails'])->map(function ($detail) use ($products) {
                    $product = $products->firstWhere('id', $detail['product_id']);
                    if ($product) {
                        $detail['product_name'] = $product['name'];
                    }
                    return $detail;
                })->toArray();
            } else {
                $invoice['products'] = [];
            }
            return $invoice;
        });

        return view('pages.invoice.invoice', compact('invoices'));
    } catch (\Throwable $e) {
        \Log::error('Error fetching invoices: ' . $e->getMessage());
        return view('pages.error')->with([
            'errorCode' => $e->getCode(),
            'errorMessage' => $e->getMessage()
        ]);
    }
}

public function deleteInvoice($id)
{
    try {
        $response = Http::delete("https://api.gsutil.xyz/invoice/$id");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete invoice']);
        }
    } catch (\Exception $e) {
        \Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error: Unable to delete invoice']);
    }
}    

}
