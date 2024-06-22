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
                        <div class="table-responsive">
                            <table id="outstandingTable" class="table table-bordered table-striped">
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
                                <tbody id="outstandingTableBody">
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
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
    </style>
    <script>
        $(document).ready(function() {
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

                const tableBody = $('#outstandingTableBody');
                tableBody.empty();

                if (data.clients && data.clients.length) {
                    let rowIndex = 1;
                    data.clients.forEach((client) => {
                        let clientRowSpan = client.invoices.length;

                        client.invoices.forEach((invoice, index) => {
                            const row = `<tr>
                                <td>${index === 0 ? rowIndex : ''}</td>
                                <td>${index === 0 ? client.name : ''}</td>
                                <td>${index === 0 ? (client.phone_no || 'N/A') : ''}</td>
                                <td>${index === 0 ? parseFloat(client.total_amount).toFixed(2) : ''}</td>
                                <td>${index === 0 ? parseFloat(client.paid_amount).toFixed(2) : ''}</td>
                                <td>${index === 0 ? parseFloat(client.total_outstanding_balance).toFixed(2) : ''}</td>
                                <td>${invoice.reference_number}</td>
                                <td><span class="${new Date(invoice.credit_period_end_date) < new Date() ? 'text-danger' : (new Date(invoice.credit_period_end_date) - new Date() < 2 * 24 * 60 * 60 * 1000 ? 'text-warning' : 'text-success')}">${new Date(invoice.credit_period_end_date).toLocaleDateString()}</span></td>
                                <td>${parseFloat(invoice.total_amount).toFixed(2)}</td>
                                <td>${parseFloat(invoice.balance).toFixed(2)}</td>
                                <td>${parseFloat(invoice.paid_amount).toFixed(2)}</td>
                            </tr>`;
                            tableBody.append(row);
                        });

                        rowIndex++;
                    });
                } else {
                    const row = '<tr><td colspan="11">No records found</td></tr>';
                    tableBody.append(row);
                }

                // Initialize DataTables with buttons
                $('#outstandingTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    pageLength: 15,
                    lengthMenu: [10, 15, 25, 50, 100]
                });

                updatePaidVsUnpaidChart(data.overall.paid_percentage, data.overall.unpaid_percentage);
            }

            function updatePaidVsUnpaidChart(paidPercentage, unpaidPercentage) {
                const ctx = document.getElementById('paidVsUnpaidChart').getContext('2d');

                if (paidVsUnpaidChart) {
                    paidVsUnpaidChart.destroy();
                }

                const data = {
                    labels: ['Paid', 'Unpaid'],
                    datasets: [{
                        data: [paidPercentage, unpaidPercentage],
                        backgroundColor: ['#28a745', '#dc3545']
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
