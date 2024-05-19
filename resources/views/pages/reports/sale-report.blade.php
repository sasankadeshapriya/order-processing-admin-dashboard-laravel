@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Report Filter -->
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="filter">Filter by:</label>
                            <select class="form-control" id="filter">
                                <option value="week">Week</option>
                                <option value="day">Day</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                                <option value="all">All Time</option>
                                <option value="custom">Custom Date Range</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-2" id="custom-date-range" style="display: none;">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="start-date">Start Date:</label>
                            <input type="date" class="form-control" id="start-date">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="end-date">End Date:</label>
                            <input type="date" class="form-control" id="end-date">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button id="apply-custom-filter" class="btn btn-primary">Apply</button>
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

                <div class="row mb-2">
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
    <script>
        $(document).ready(function() {
            let paymentOptionsChart;
            let salesLineChart;

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
                let url = `/api/sales/report?filter=${filter}`;
                if (filter === 'custom' && startDate && endDate) {
                    url = `/api/sales/report?start_date=${startDate}&end_date=${endDate}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        // Sort the sales data by date to ensure correct plotting
                        if (data.sales && data.sales.length) {
                            data.sales.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
                        }
                        updateReport(data, filter);
                    },
                    error: function() {
                        alert('Failed to fetch sales report');
                    }
                });
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
                updateSalesLineChart(data.sales || [], filter);
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
                        backgroundColor: hasData ? ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'] : [
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
                                    color: hasData ? 'black' : 'grey'
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

            $('#filter').change(function() {
                if ($(this).val() === 'custom') {
                    $('#custom-date-range').show();
                } else {
                    $('#custom-date-range').hide();
                    fetchReport($(this).val());
                }
            });

            $('#apply-custom-filter').click(function() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                if (startDate && endDate) {
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
