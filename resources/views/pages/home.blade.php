@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        @media (max-width: 767px) {
            .small-box .inner h3 {
                font-size: 1.25em;
                /* Adjust size as needed to match <h4> */
                margin-top: 0.5em;
                /* Adjust top margin as needed */
                margin-bottom: 0.5em;
                /* Adjust bottom margin as needed */
            }
        }
    </style>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h3>Invoices</h3>
                                <p>LKR {{ number_format($totalAmount, 2) }}</p>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-three"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h3>Payments</h3>
                                <p>LKR {{ number_format($totalPaidAmount, 2) }}</p>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-two"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h3>Products</h3>
                                <p>{{ $productCount }} Products</p>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-one"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h3>Route</h3>
                                <p>{{ $routeCount }} Routes</p>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-four"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <!-- Bar chart -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Product Sales (This Month)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="salesChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($soldProductDetails, 'product_name')) !!},
                    datasets: [{
                        label: 'Total Quantity Sold',
                        data: {!! json_encode(array_column($soldProductDetails, 'total_quantity')) !!},
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            display: true,
                        },
                        y: {
                            display: true,
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
