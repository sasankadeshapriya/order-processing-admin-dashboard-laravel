@extends('layouts.app')

@section('title', 'MRP Report')

@section('content')
    <!-- Loading Indicator -->
    <div class="content-wrapper" id="loading-indicator" style="display: none;">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="spinner-border" style="width: 3rem; height: 3rem; color: #C8B400;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <!-- Content Section -->
    <div class="content-wrapper" id="content-section" style="display: none;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Maximum Retail Price (MRP) Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">MRP Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <canvas id="mrpChart" style="height: 125px;"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showLoadingIndicator();

            fetch('{{ route('mrp-report.data') }}')
                .then(response => {
                    if (!response.ok) throw new Error('Failed to load MRP data');
                    return response.json();
                })
                .then(data => {
                    buildChart(data);
                    hideLoadingIndicator();
                    document.getElementById('content-section').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '/report-error'; // Assuming '/error-page' is your error route
                });

            function showLoadingIndicator() {
                $('#loading-indicator').show();
                $('#content-section').hide();
            }

            function hideLoadingIndicator() {
                $('#loading-indicator').hide();
                $('#content-section').show();
            }

            function buildChart(data) {
                const ctx = document.getElementById('mrpChart').getContext('2d');
                const productNames = data.map(item => item.Product.name);
                const buyPrices = data.map(item => parseFloat(item.buy_price));
                const cashPrices = data.map(item => parseFloat(item.cash_price));
                const checkPrices = data.map(item => parseFloat(item.check_price));
                const creditPrices = data.map(item => parseFloat(item.credit_price));

                const mrpChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: productNames,
                        datasets: [{
                            label: 'MRP',
                            data: buyPrices,
                            borderColor: '#007bff',
                            backgroundColor: 'transparent',
                            fill: false
                        }, {
                            label: 'Cash Price',
                            data: cashPrices,
                            borderColor: '#28a745',
                            backgroundColor: 'transparent',
                            fill: false
                        }, {
                            label: 'Check Price',
                            data: checkPrices,
                            borderColor: '#ffc107',
                            backgroundColor: 'transparent',
                            fill: false
                        }, {
                            label: 'Credit Price',
                            data: creditPrices,
                            borderColor: '#dc3545',
                            backgroundColor: 'transparent',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
