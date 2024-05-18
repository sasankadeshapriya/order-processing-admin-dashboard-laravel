<?php

namespace App\Http\Controllers;


class ReportController extends Controller
{

    public function showSales()
    {
        return view('pages.reports.sale-report');
    }

}
