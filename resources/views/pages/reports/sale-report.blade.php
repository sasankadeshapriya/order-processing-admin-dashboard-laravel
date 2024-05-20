@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
    <div class="content-wrapper" id="loading-indicator" style="display: none;">
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="spinner-border" style="width: 3rem; height: 3rem; color: #C8B400;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="content-wrapper" id="content-section" style="display: none;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- Button group for all screen sizes -->
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="btn-group" role="group" style="display: inline-flex; flex-wrap: nowrap;">
                            <button type="button" class="btn filter-btn active" data-filter="week"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Week</button>
                            <button type="button" class="btn filter-btn" data-filter="day"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Day</button>
                            <button type="button" class="btn filter-btn" data-filter="month"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Month</button>
                            <button type="button" class="btn filter-btn" data-filter="year"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Year</button>
                            <button type="button" class="btn filter-btn" data-filter="all"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Life Time</button>
                            <button type="button" class="btn filter-btn" data-filter="custom"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Custom Range</button>
                        </div>
                    </div>
                </div>

                <div class="row mb-2" id="custom-date-range" style="display: none;">
                    <div class="col-12 col-sm-auto">
                        <div class="form-group">
                            <input type="date" class="form-control" id="start-date">
                        </div>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <div class="form-group">
                            <input type="date" class="form-control" id="end-date">
                        </div>
                    </div>
                    <div class="col-12 col-sm-auto align-self-end">
                        <div class="form-group">
                            <button id="apply-custom-filter" class="btn filter-btn" style="width: 100%;">Apply</button>
                        </div>
                    </div>
                </div>
                <!-- Small boxes (Stat box) -->
                <div class="row mb-2">
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Sales</h4>
                                <h3 id="totalSalesSum">0.00</h3>
                                <div
                                    style="display: inline-block; background-color: #d9d9d9; padding: 2px 5px; border-radius: 2px; margin-left: 0px; margin-bottom: 10px;">
                                    <p id="salesComparison" style="margin: 0;">Comparison text</p>
                                </div>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-three"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Paid</h4>
                                <h3 id="totalPaidSum">0.00</h3>
                                <div
                                    style="display: inline-block; background-color: #d9d9d9; padding: 2px 5px; border-radius: 2px; margin-left: 0px; margin-bottom: 10px;">
                                    <p id="paidComparison" style="margin: 0;">Comparison text</p>
                                </div>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-two"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Balance</h4>
                                <h3 id="totalBalanceSum">0.00</h3>
                                <div
                                    style="display: inline-block; background-color: #d9d9d9; padding: 2px 5px; border-radius: 2px; margin-left: 0px; margin-bottom: 10px;">
                                    <p id="balanceComparison" style="margin: 0;">Comparison text</p>
                                </div>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-one"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-md-6">
                        <!-- Pie CHART -->
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Distribution of Payment Options</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="paymentOptionsChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Line CHART -->
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Sales Over Time</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="salesLineChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- Most Sold Products CHART -->
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Most Sold Products</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="mostSoldProductsChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Previous Period Sums and Top Clients -->
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Previous Period Sums & Top Clients</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <p><strong>Previous Total Sales Sum:</strong> <span
                                            id="previousTotalSalesSum">0.00</span></p>
                                    <p><strong>Previous Total Paid Sum:</strong> <span
                                            id="previousTotalPaidSum">0.00</span></p>
                                    <p><strong>Previous Total Balance Sum:</strong> <span
                                            id="previousTotalBalanceSum">0.00</span></p>
                                    <p><strong>Top Clients:</strong></p>
                                    <ul id="topClientsList">
                                        <li id="topClient1">N/A</li>
                                        <li id="topClient2">N/A</li>
                                        <li id="topClient3">N/A</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <p>All prices in <code>.LKR</code> format.</p>
                                <div class="table-responsive">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Reference Number</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Balance</th>
                                                <th>Payment Option</th>
                                                <th>Client</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Sales data will be dynamically added by JavaScript --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Reference Number</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Balance</th>
                                                <th>Payment Option</th>
                                                <th>Client</th>
                                                <th>Date</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <!-- Include Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <style>
        .filter-btn {
            border-radius: 0px;
            background-color: #6C757D;
            border: 1px solid #6C757D;
            color: white;
        }

        .filter-btn.active,
        .filter-btn:focus,
        .filter-btn:hover {
            background-color: #343A40;
            color: white;
        }
    </style>
    <script>
        $(document).ready(function() {
            let paymentOptionsChart;
            let salesLineChart;
            let mostSoldProductsChart;
            let currentFilter = 'week';
            let currentStartDate = null;
            let currentEndDate = null;

            function initializeDataTable() {
                if (!$.fn.DataTable.isDataTable('#example3')) {
                    $('#example3').DataTable({
                        "responsive": false,
                        "lengthChange": true,
                        "autoWidth": false,
                        "buttons": ["excel", "pdf", "print", "colvis"]
                    }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
                }
            }

            function fetchReport(filter, startDate = null, endDate = null) {
                showLoadingIndicator();

                let url = `/api/sales/report?filter=${filter}`;
                if (filter === 'custom' && startDate && endDate) {
                    url = `/api/sales/report?start_date=${startDate}&end_date=${endDate}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        hideLoadingIndicator();
                        updateReport(data, filter);
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

            function updateReport(data, filter) {
                $('#totalSalesSum').text(data.totalSalesSum.toFixed(2));
                const salesComparison = parseFloat(data.salesComparison);
                const salesComparisonHtml = salesComparison > 0 ?
                    `<i class="bi bi-caret-up-fill" style="color: green;"></i> <b><span style="color: green;">${salesComparison.toFixed(2)}%</span></b>` :
                    `<i class="bi bi-caret-down-fill" style="color: red;"></i> <b><span style="color: red;">${salesComparison.toFixed(2)}%</span></b>`;
                $('#salesComparison').html(salesComparisonHtml);

                $('#totalPaidSum').text(data.totalPaidSum.toFixed(2));
                const paidComparison = parseFloat(data.paidComparison);
                const paidComparisonHtml = paidComparison > 0 ?
                    `<i class="bi bi-caret-up-fill" style="color: green;"></i> <b><span style="color: green;">${paidComparison.toFixed(2)}%</span></b>` :
                    `<i class="bi bi-caret-down-fill" style="color: red;"></i> <b><span style="color: red;">${paidComparison.toFixed(2)}%</span></b>`;
                $('#paidComparison').html(paidComparisonHtml);

                $('#totalBalanceSum').text(data.totalBalanceSum.toFixed(2));
                const balanceComparison = parseFloat(data.balanceComparison);
                const balanceComparisonHtml = balanceComparison > 0 ?
                    `<i class="bi bi-caret-up-fill" style="color: green;"></i> <b><span style="color: green;">${balanceComparison.toFixed(2)}%</span></b>` :
                    `<i class="bi bi-caret-down-fill" style="color: red;"></i> <b><span style="color: red;">${balanceComparison.toFixed(2)}%</span></b>`;
                $('#balanceComparison').html(balanceComparisonHtml);

                $('#previousTotalSalesSum').text(data.previousPeriodSums.previousTotalSalesSum.toFixed(2));
                $('#previousTotalPaidSum').text(data.previousPeriodSums.previousTotalPaidSum.toFixed(2));
                $('#previousTotalBalanceSum').text(data.previousPeriodSums.previousTotalBalanceSum.toFixed(2));

                let salesTable = $('#example3').DataTable();
                salesTable.clear();

                if (data.sales && data.sales.length) {
                    data.sales.forEach(sale => {
                        salesTable.row.add([
                            sale.reference_number,
                            sale.total_amount,
                            sale.paid_amount,
                            sale.balance,
                            sale.payment_option,
                            sale.Client.name,
                            new Date(sale.createdAt).toLocaleString()
                        ]).draw();
                    });
                } else {
                    salesTable.row.add(['No records found', '', '', '', '', '', '']).draw();
                }

                updatePaymentOptionsChart(data.paymentOptionPercentages);
                updateSalesLineChart(data.sales || [], filter, currentStartDate, currentEndDate);
                updateMostSoldProductsChart(data.mostSoldProducts || []);

                updateTopClients(data.sales || []);
            }

            function updatePaymentOptionsChart(paymentOptionPercentages) {
                const ctx = document.getElementById('paymentOptionsChart').getContext('2d');

                if (paymentOptionsChart) {
                    paymentOptionsChart.destroy();
                }

                const processedData = paymentOptionPercentages.map(option => isNaN(parseFloat(option.percentage)) ?
                    0 : parseFloat(option.percentage));
                const hasData = processedData.some(value => value > 0);

                const data = {
                    labels: hasData ? paymentOptionPercentages.map(option => option.option) : ['No Data'],
                    datasets: [{
                        data: hasData ? processedData : [1],
                        backgroundColor: hasData ? ['#DBF2F2', '#b6e4e4', '#c8ebeb', '#eef9f9'] : [
                            '#343A40'
                        ],
                    }]
                };

                paymentOptionsChart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: hasData ? 'grey' : 'grey'
                                }
                            }
                        }
                    }
                });
            }

            function generateLabels(filter, startDate = null, endDate = null) {
                const labels = [];
                const now = new Date();
                switch (filter) {
                    case 'day':
                        for (let i = 0; i < 24; i += 2) {
                            labels.push(`${i.toString().padStart(2, '0')}:00`);
                        }
                        break;
                    case 'week':
                        labels.push('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                        break;
                    case 'month':
                        const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
                        for (let i = 1; i <= daysInMonth; i++) {
                            labels.push(
                                `${(now.getMonth() + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`
                            );
                        }
                        break;
                    case 'year':
                        const months = ["January", "February", "March", "April", "May", "June", "July", "August",
                            "September", "October", "November", "December"
                        ];
                        months.forEach(month => labels.push(month));
                        break;
                    case 'all':
                        const currentYear = now.getFullYear();
                        for (let i = currentYear - 2; i <= currentYear + 5; i++) {
                            labels.push(i.toString());
                        }
                        break;
                    case 'custom':
                        if (startDate && endDate) {
                            let current = new Date(startDate);
                            const end = new Date(endDate);
                            while (current <= end) {
                                labels.push(current.toISOString().split('T')[0]);
                                current.setDate(current.getDate() + 1);
                            }
                        }
                        break;
                    default:
                        break;
                }
                return labels;
            }

            function updateSalesLineChart(sales, filter, startDate = null, endDate = null) {
                const ctx = document.getElementById('salesLineChart').getContext('2d');
                const labels = generateLabels(filter, startDate, endDate);
                const dataPoints = {};

                sales.forEach(sale => {
                    const date = new Date(sale.createdAt);
                    let label;
                    switch (filter) {
                        case 'day':
                            label = `${date.getHours().toString().padStart(2, '0')}:00`;
                            break;
                        case 'week':
                            label = date.toLocaleString('en-CA', {
                                weekday: 'long'
                            });
                            break;
                        case 'month':
                            label =
                                `${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
                            break;
                        case 'year':
                            label = date.toLocaleString('en-CA', {
                                month: 'long'
                            });
                            break;
                        case 'all':
                            label = date.getFullYear().toString();
                            break;
                        case 'custom':
                            label = date.toISOString().split('T')[0];
                            break;
                        default:
                            label = date.toLocaleDateString('en-CA');
                    }
                    if (!dataPoints[label]) {
                        dataPoints[label] = 0;
                    }
                    dataPoints[label] += parseFloat(sale.total_amount);
                });

                const chartData = labels.map(label => dataPoints[label] || 0);

                if (salesLineChart) {
                    salesLineChart.destroy();
                }

                let timeUnit, displayFormats;
                switch (filter) {
                    case 'day':
                        timeUnit = 'hour';
                        displayFormats = {
                            hour: 'hA'
                        };
                        break;
                    case 'week':
                        timeUnit = 'day';
                        displayFormats = {
                            day: 'EEEE'
                        };
                        break;
                    case 'month':
                        timeUnit = 'day';
                        displayFormats = {
                            day: 'MM-dd'
                        };
                        break;
                    case 'year':
                        timeUnit = 'month';
                        displayFormats = {
                            month: 'MMMM'
                        };
                        break;
                    case 'all':
                        timeUnit = 'year';
                        displayFormats = {
                            year: 'yyyy'
                        };
                        break;
                    case 'custom':
                        timeUnit = 'day';
                        displayFormats = {
                            day: 'yyyy-MM-dd'
                        };
                        break;
                    default:
                        timeUnit = 'day';
                        displayFormats = {
                            day: 'MMM dd'
                        };
                }

                salesLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Sales',
                            data: chartData.map(value => parseFloat(value.toFixed(2))),
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                            pointStyle: 'circle',
                            radius: 5,
                            hoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                type: 'category',
                                time: {
                                    unit: timeUnit,
                                    displayFormats: displayFormats,
                                    tooltipFormat: 'MMM dd, yyyy'
                                },
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return `Sales: ${tooltipItem.parsed.y.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function updateMostSoldProductsChart(mostSoldProducts) {
                const ctx = document.getElementById('mostSoldProductsChart').getContext('2d');

                if (mostSoldProductsChart) {
                    mostSoldProductsChart.destroy();
                }

                const data = {
                    labels: mostSoldProducts.map(product => product.productName),
                    datasets: [{
                        label: 'Quantity Sold',
                        data: mostSoldProducts.map(product => parseFloat(product.totalQuantity)),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                };

                mostSoldProductsChart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Product Name'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quantity Sold'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            function updateTopClients(sales) {
                const clientTotals = {};

                sales.forEach(sale => {
                    const clientId = sale.Client.id;
                    const clientName = sale.Client.name;
                    const totalAmount = parseFloat(sale.total_amount);

                    if (!clientTotals[clientId]) {
                        clientTotals[clientId] = {
                            name: clientName,
                            total: 0
                        };
                    }

                    clientTotals[clientId].total += totalAmount;
                });

                const sortedClients = Object.entries(clientTotals)
                    .sort(([, a], [, b]) => b.total - a.total)
                    .slice(0, 3);

                $('#topClient1').text(sortedClients[0] ?
                    `${sortedClients[0][1].name} (ID: ${sortedClients[0][0]})` : 'N/A');
                $('#topClient2').text(sortedClients[1] ?
                    `${sortedClients[1][1].name} (ID: ${sortedClients[1][0]})` : 'N/A');
                $('#topClient3').text(sortedClients[2] ?
                    `${sortedClients[2][1].name} (ID: ${sortedClients[2][0]})` : 'N/A');
            }

            function isValidDateRange(startDate, endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                return start <= end;
            }

            $('.filter-btn').click(function() {
                const filter = $(this).data('filter');
                currentFilter = filter;
                currentStartDate = null;
                currentEndDate = null;

                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                if (filter === 'custom') {
                    $('#custom-date-range').show();
                } else {
                    $('#custom-date-range').hide();
                    fetchReport(filter);
                }
            });

            $('#apply-custom-filter').click(function() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                if (startDate && endDate) {
                    if (!isValidDateRange(startDate, endDate)) {
                        alert('Start date should be before end date.');
                        return;
                    }
                    currentStartDate = startDate;
                    currentEndDate = endDate;
                    fetchReport('custom', startDate, endDate);
                } else {
                    alert('Please select both start and end dates.');
                }
            });

            // Load the default report (week) on page load
            initializeDataTable();
            fetchReport('week');
        });
    </script>
@endsection
