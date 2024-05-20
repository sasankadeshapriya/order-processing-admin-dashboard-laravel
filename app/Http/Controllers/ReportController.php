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
        // Log the incoming request data
        \Log::info('Request Data:', $request->all());

        // Determine the filter based on the request
        $filter = $request->query('filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $url = 'http://localhost:4000/sales/report';

        // Log the determined filter and date range
        \Log::info('Filter:', ['filter' => $filter]);
        \Log::info('Start Date:', ['start_date' => $startDate]);
        \Log::info('End Date:', ['end_date' => $endDate]);

        // Construct the URL with query parameters
        if ($startDate && $endDate) {
            $url .= '?start_date=' . $startDate . '&end_date=' . $endDate;
        } else if ($filter && $filter !== 'all') {
            $url .= '?' . $filter . '=true';
        }

        // Log the constructed URL
        \Log::info('Constructed URL:', ['url' => $url]);

        // Make the request to the Node.js API
        $response = Http::get($url);

        // Log the response status and body
        \Log::info('API Response Status:', ['status' => $response->status()]);
        \Log::info('API Response Body:', ['body' => $response->body()]);

        // Return the response from the Node.js API
        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['message' => 'Failed to fetch sales report'], 500);
    }
}
