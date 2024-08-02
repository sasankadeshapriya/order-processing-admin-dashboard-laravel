<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrashController extends Controller
{
    private $models = [
        'Product',
        'Batch',
        'Client',
        'Commission',
        'Employee',
        'Invoice',
        'InvoiceDetail',
        'Payment',
        'Assignment',
        'Route',
        'Vehicle_inventory',
        'Vehicle'
    ];

    public function showTrash()
    {
        return view('pages.Trash.trash', ['models' => $this->models]);
    }

    public function getDeletedRecords($model)
    {
        $url = env('API_URL') . "/trash/deletedRecords/{$model}";

        $response = Http::get($url);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['message' => 'Failed to fetch deleted records'], 500);
    }

    public function restoreRecord($model, $id)
    {
        $url = env('API_URL') . "/trash/restore/{$model}/{$id}";

        try {
            $response = Http::put($url);

            if ($response->successful()) {
                return response()->json(['message' => $response->json()['message']]);
            }
        } catch (RequestException $e) {
            Log::error('Error restoring record: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Failed to restore record'], 500);
    }


}
