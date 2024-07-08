@extends('layouts.app')

@section('title', 'Outstanding Balance Report')

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
                        <h1>Outstanding Balance Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Outstanding Balance Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Overall</h3>

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
                                <div class="small-box custom-bg-color-small-box">
                                    <div class="inner">
                                        <h4>Total Sales</h4>
                                        <h3 id="totalSalesSum">0.00</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="custom-icon-three"></i>
                                    </div>
                                </div>

                                <div class="small-box custom-bg-color-small-box">
                                    <div class="inner">
                                        <h4>Total Paid</h4>
                                        <h3 id="totalPaidSum">0.00</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="custom-icon-two"></i>
                                    </div>
                                </div>

                                <div class="small-box custom-bg-color-small-box">
                                    <div class="inner">
                                        <h4>Total Balance</h4>
                                        <h3 id="totalBalanceSum">0.00</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="custom-icon-one"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Pie Chart -->
                        <div class="card">
                            <div class="card-header custom-bg-color-small-box">
                                <h3 class="card-title">Overall Paid vs Unpaid</h3>

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
                                    <canvas id="paidVsUnpaidChart"
                                        style="min-height: 380px; height: 250px; max-height: 380px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p>
                            <span class="badge bg-danger">Red</span> indicates the date has been exceeded.
                            <span class="badge bg-success">Green</span> indicates only two days remain.
                        </p>
                        <div class="table-responsive">
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid</th>
                                        <th>Total Outstanding Balance</th>
                                        <th>Reference Number</th>
                                        <th>Credit Period End Date</th>
                                        <th>Invoice Amount</th>
                                        <th>Outstanding Balance</th>
                                        <th>Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sales data will be dynamically added by JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid</th>
                                        <th>Total Outstanding Balance</th>
                                        <th>Reference Number</th>
                                        <th>Credit Period End Date</th>
                                        <th>Invoice Amount</th>
                                        <th>Outstanding Balance</th>
                                        <th>Paid Amount</th>
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .bg-danger {
            background-color: rgba(220, 53, 69, 0.2);
            /* Bootstrap red with opacity 0.2 */
            color: white;
        }

        .bg-success {
            background-color: rgba(40, 244, 88, 0.2);
            /* Bootstrap green with opacity 0.2 */
            color: white;
        }
    </style>
    <script>
        $(document).ready(function() {

            function initializeDataTable() {
                if (!$.fn.DataTable.isDataTable('#example3')) {
                    $('#example3').DataTable({
                        "responsive": false,
                        "lengthChange": true,
                        "autoWidth": false,
                        "buttons": ["excel", "pdf", "print", "colvis"],
                        "order": [
                            [7, 'asc']
                        ], // Sorting by credit period end date in descending order.
                        "createdRow": function(row, data, dataIndex) {
                            // Ensure data[7] is the date string in 'MM/DD/YYYY' format
                            const creditDateString = data[7];
                            const creditDate = new Date(creditDateString);
                            const currentDate = new Date();
                            currentDate.setHours(0, 0, 0, 0); // Normalize current date
                            const twoDaysAhead = new Date();
                            twoDaysAhead.setDate(currentDate.getDate() + 2); // Set two days ahead
                            twoDaysAhead.setHours(0, 0, 0, 0); // Normalize two days ahead date

                            console.log(
                                `Checking date: ${creditDateString}, Credit Date: ${creditDate.toDateString()}, Current Date: ${currentDate.toDateString()}, Two Days Ahead: ${twoDaysAhead.toDateString()}`
                            );

                            if (creditDate < currentDate) {
                                $(row).addClass('bg-danger text-white');
                                console.log('Adding red class');
                            } else if (creditDate <= twoDaysAhead) {
                                $(row).addClass('bg-success text-white');
                                console.log('Adding green class');
                            }
                        }
                    }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
                }
            }

            // Call initializeDataTable function when document is ready
            initializeDataTable();


            let paidVsUnpaidChart;

            function fetchOutstandingReport(filter, startDate = null, endDate = null) {
                showLoadingIndicator();

                let url = `/api/outstanding/report?filter=${filter}`;
                if (filter === 'custom' && startDate && endDate) {
                    url = `/api/outstanding/report?start_date=${startDate}&end_date=${endDate}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        console.log('Data fetched successfully:', data);
                        hideLoadingIndicator();
                        updateOutstandingReport(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
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

            function updateOutstandingReport(data) {
                $('#totalSalesSum').text(parseFloat(data.overall.total_amount).toFixed(2) || '0.00');
                $('#totalPaidSum').text(parseFloat(data.overall.total_paid).toFixed(2) || '0.00');
                $('#totalBalanceSum').text(parseFloat(data.overall.total_balance).toFixed(2) || '0.00');

                let salesTable = $('#example3').DataTable();
                salesTable.clear();

                let rowIndex = 1; // Initialize row index for numbering.
                if (data.clients && data.clients.length) {
                    data.clients.forEach(client => {
                        client.invoices.forEach(invoice => {
                            salesTable.row.add([
                                rowIndex.toString(), // Display row number
                                client.name,
                                client.phone_no || 'N/A',
                                parseFloat(client.total_amount).toFixed(2),
                                parseFloat(client.paid_amount).toFixed(2),
                                parseFloat(client.total_outstanding_balance).toFixed(2),
                                invoice.reference_number,
                                new Date(invoice.credit_period_end_date)
                                .toLocaleDateString(),
                                parseFloat(invoice.total_amount).toFixed(2),
                                parseFloat(invoice.balance).toFixed(2),
                                parseFloat(invoice.paid_amount).toFixed(2)
                            ]);
                            rowIndex++; // Increment row index after adding each row.
                        });
                    });
                    salesTable.draw();
                } else {
                    salesTable.row.add(['No records found', '', '', '', '', '', '', '', '', '', '']).draw();
                }

                updatePaidVsUnpaidChart(data.overall.paid_percentage, data.overall.unpaid_percentage);
            }

            function updatePaidVsUnpaidChart(paidPercentage, unpaidPercentage) {
                const ctx = document.getElementById('paidVsUnpaidChart').getContext('2d');

                if (paidVsUnpaidChart) {
                    paidVsUnpaidChart.destroy();
                }

                // Check for valid percentage values or create a default 'No data' chart
                if (isNaN(paidPercentage) || isNaN(unpaidPercentage)) {
                    const data = {
                        labels: ['No data available'],
                        datasets: [{
                            data: [100],
                            backgroundColor: ['#343A40'],
                            hoverBackgroundColor: ['#343A40']
                        }]
                    };

                    paidVsUnpaidChart = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            }
                        }
                    });
                } else {
                    const data = {
                        labels: ['Paid', 'Unpaid'],
                        datasets: [{
                            data: [paidPercentage, unpaidPercentage],
                            backgroundColor: ['#DBF2F2', '#b6e4e4']
                        }]
                    };

                    paidVsUnpaidChart = new Chart(ctx, {
                        type: 'pie',
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            let label = tooltipItem.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += `${tooltipItem.raw.toFixed(2)}%`;
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            $('.filter-btn').click(function() {
                const filter = $(this).data('filter');

                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                if (filter === 'custom') {
                    $('#custom-date-range').show();
                } else {
                    $('#custom-date-range').hide();
                    fetchOutstandingReport(filter);
                }
            });

            $('#apply-custom-filter').click(function() {
                const startDate = $('#start-date').val();
                const endDate = $('#end-date').val();

                if (startDate && endDate) {
                    fetchOutstandingReport('custom', startDate, endDate);
                } else {
                    alert('Please select both start and end dates.');
                }
            });

            // Load the default report (week) on page load
            fetchOutstandingReport('week');
        });
    </script>
@endsection
