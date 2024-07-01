@extends('layouts.app')

@section('title', 'Overall Day End Report')

@section('content')
    <div class="content-wrapper" id="loading-indicator" style="display: none;">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; color: #C8B400;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="content-wrapper" id="content-section" style="display: none;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Overall Day End Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Overall Day End Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row mb-2">
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Sales</h4>
                                <h3 id="totalSalesSum">0.00</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Commission</h4>
                                <h3 id="totalCommissionSum">0.00</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Assignments Count</h4>
                                <h3 id="assignmentsCount">0</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Received Card -->
                <div class="card">
                    <div class="card-header custom-bg-color-small-box">
                        <h3 class="card-title">Payments Received</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentsChart" style="height: 250px;"></canvas>
                    </div>
                </div>

                <!-- Products Sold Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsSoldTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>SKU</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Products data will be dynamically added by JavaScript --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>SKU</th>
                                        <th>Quantity</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            function fetchDayEndReport() {
                showLoadingIndicator();

                $.ajax({
                    url: '/api/day-end/report',
                    method: 'GET',
                    success: function(data) {
                        hideLoadingIndicator();
                        updateDayEndReport(data);
                    },
                    error: function() {
                        window.location.href = '/report-error';
                    }
                });
            }

            function showLoadingIndicator() {
                $('#loading-indicator').show();
                $('#content-section').hide();
            }

            function hideLoadingIndicator() {
                $('#loading-indicator').hide();
                $('#content-section').show();
            }

            function updateDayEndReport(data) {
                $('#totalSalesSum').text(data.totalSales.toFixed(2));
                $('#totalCommissionSum').text(data.totalCommission.toFixed(2));
                $('#assignmentsCount').text(data.assignmentsCount);

                updatePaymentsChart(data.paymentsReceived);
                updateProductsSoldTable(data.productsSold);
            }

            function updatePaymentsChart(payments) {
                const ctx = document.getElementById('paymentsChart').getContext('2d');
                const labels = payments.map(payment => payment.payment_option);
                const dataPoints = payments.map(payment => parseFloat(payment.total_amount));

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Amount Received',
                            data: dataPoints,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function updateProductsSoldTable(products) {
                let table = $('#productsSoldTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "buttons": ["excel", "pdf", "print", "colvis"],
                    "dom": 'Bfrtip'
                });

                table.clear();

                products.forEach(product => {
                    table.row.add([
                        product.productName,
                        product.sku,
                        product.quantity,
                        product.productCode
                    ]).draw();
                });
            }

            fetchDayEndReport();
        });
    </script>
@endsection
