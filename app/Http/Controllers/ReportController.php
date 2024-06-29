<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    public function showSales()
    {
        return view('pages.reports.sale-report');
    }

    public function outstandingSales()
    {
        return view('pages.reports.outstanding-report');
    }

    public function getSalesReport(Request $request)
    {
        $filter = $request->query('filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $url = 'http://api.gsutil.xyz/sales/report';

        if ($startDate && $endDate) {
            $url .= '?start_date=' . $startDate . '&end_date=' . $endDate;
        } else if ($filter && $filter !== 'all') {
            $url .= '?' . $filter . '=true';
        }

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['message' => 'Failed to fetch sales report'], 500);
    }

    public function getOutstandingReport(Request $request)
    {
        $filter = $request->query('filter', 'week');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $url = 'http://localhost:4000/outstanding';

        if ($startDate && $endDate) {
            $url .= '?filter=custom&start_date=' . $startDate . '&end_date=' . $endDate;
        } else {
            $url .= '?filter=' . $filter;
        }

        $response = Http::get($url);

        //Log::info($response);
        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['message' => 'Failed to fetch outstanding report'], 500);
    }


    public function showCommission()
    {
        return view('pages.reports.comission-report');
    }

    public function getCommissionReport(Request $request)
    {
        $filter = $request->query('filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Log the request parameters
        Log::info('Fetching commission report', [
            'filter' => $filter,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $url = 'http://localhost:4000/commission/report';

        if ($startDate && $endDate) {
            $url .= '?filter=custom&start_date=' . $startDate . '&end_date=' . $endDate;
        } else if ($filter) {
            $url .= '?filter=' . $filter;
        }

        // Log the constructed URL
        Log::info('Request URL', ['url' => $url]);

        $response = Http::get($url);

        // Log the response status
        Log::info('API Response Status', ['status' => $response->status()]);

        if ($response->successful()) {
            // Log the response data
            Log::info('API Response Data', ['data' => $response->json()]);
            return $response->json();
        }

        // Log the error message
        Log::error('Failed to fetch commission report', ['message' => $response->body()]);

        return response()->json(['message' => 'Failed to fetch commission report'], 500);
    }
}
