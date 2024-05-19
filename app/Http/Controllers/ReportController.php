<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{

    public function showSales()
    {
        return view('pages.reports.sale-report');
    }

    public function getSalesReport(Request $request)
    {
        // Determine the filter based on the request
        $filter = $request->query('filter', 'week');
        $url = 'http://localhost:4000/sales/report';

        if ($filter !== 'all') {
            $url .= '?' . $filter . '=true';
        }

        // Make the request to the Node.js API
        $response = Http::get($url);

        // Return the response from the Node.js API
        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['message' => 'Failed to fetch sales report'], 500);
    }
}
