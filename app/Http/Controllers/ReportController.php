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
}
