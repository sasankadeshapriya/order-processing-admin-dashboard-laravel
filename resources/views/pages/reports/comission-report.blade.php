@extends('layouts.app')

@section('title', 'Commission Report')

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
                        <h1>Commission Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Commission Report</li>
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
                            <button type="button" class="btn filter-btn" data-filter="week"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Week</button>
                            <button type="button" class="btn filter-btn" data-filter="day"
                                style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Day</button>
                            <button type="button" class="btn filter-btn active" data-filter="month"
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

                <div class="row mb-2">
                    <div class="col-lg-6 col-6">
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Total Commission</h4>
                                <h3 id="totalCommission">0.00</h3>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-three"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-6">
                        <div class="small-box custom-bg-color-small-box">
                            <div class="inner">
                                <h4>Top 10 Employees</h4>
                                <ul id="top10EmployeesList">
                                    <!-- Top 10 employees will be dynamically added by JavaScript -->
                                </ul>
                            </div>
                            <div class="icon">
                                <i class="custom-icon-two"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <p>All amounts in <code>.LKR</code> format.</p>
                                <div class="table-responsive">
                                    <table id="commissionTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Total Commission</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Commission data will be dynamically added by JavaScript --}}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Total Commission</th>
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
            let currentFilter = 'month';
            let currentStartDate = null;
            let currentEndDate = null;

            function initializeDataTable() {
                if (!$.fn.DataTable.isDataTable('#commissionTable')) {
                    $('#commissionTable').DataTable({
                        "responsive": false,
                        "lengthChange": true,
                        "autoWidth": false,
                        "buttons": ["excel", "pdf", "print", "colvis"]
                    }).buttons().container().appendTo('#commissionTable_wrapper .col-md-6:eq(0)');
                }
            }

            function fetchReport(filter, startDate = null, endDate = null) {
                showLoadingIndicator();

                let url = `/api/commission/report?filter=${filter}`;
                if (filter === 'custom' && startDate && endDate) {
                    url = `/api/commission/report?filter=custom&start_date=${startDate}&end_date=${endDate}`;
                }

                console.log('Fetching report with URL:', url); // Log the URL being fetched

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        console.log('Report data received:', data); // Log the data received
                        hideLoadingIndicator();
                        updateReport(data, filter);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching report:', status, error); // Log the error
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
                $('#totalCommission').text(data.total_commission);

                let top10EmployeesList = $('#top10EmployeesList');
                top10EmployeesList.empty();
                if (data.top10_employees.length) {
                    data.top10_employees.forEach(employee => {
                        top10EmployeesList.append(
                            `<li>${employee.name} (${employee.email}): ${employee.total_commission}</li>`
                            );
                    });
                } else {
                    top10EmployeesList.append('<li>No data available</li>');
                }

                let commissionTable = $('#commissionTable').DataTable();
                commissionTable.clear();

                if (data.employee_list.length) {
                    data.employee_list.forEach(employee => {
                        commissionTable.row.add([
                            employee.name,
                            employee.email,
                            employee.total_commission
                        ]).draw();
                    });
                } else {
                    commissionTable.row.add(['No records found', '', '']).draw();
                }
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

                console.log('Filter selected:', filter); // Log the selected filter

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

            // Load the default report (month) on page load
            initializeDataTable();
            fetchReport('month');
        });
    </script>
@endsection
