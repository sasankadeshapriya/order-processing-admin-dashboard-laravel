@extends('layouts.app')

@section('title', 'Trash Records')

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
                        <h1>Trash Records</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Trash Records</li>
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
                            @foreach ($models as $model)
                                <button type="button" class="btn filter-btn {{ $loop->first ? 'active' : '' }}"
                                    data-filter="{{ $model }}"
                                    style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">{{ $model }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="trashTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr id="tableHeaders">
                                                <!-- Table headers will be populated dynamically -->
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            <!-- Table data will be populated dynamically -->
                                        </tbody>
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

        .restore-btn {
            color: #28a745;
            cursor: pointer;
        }
    </style>
    <script>
        $(document).ready(function() {
            let currentFilter = 'Product';

            function showLoadingIndicator() {
                $('#loading-indicator').show();
                $('#content-section').hide();
            }

            function hideLoadingIndicator() {
                $('#loading-indicator').hide();
                $('#content-section').show();
            }

            function fetchTrashData(model) {
                showLoadingIndicator();

                let url = `/api/trash/deletedRecords/${model}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        hideLoadingIndicator();
                        updateTable(response.data);
                    },
                    error: function() {
                        window.location.href = '/report-error';
                    }
                });
            }

            function updateTable(data) {
                let tableHeaders = $('#tableHeaders');
                let tableBody = $('#tableBody');
                tableHeaders.empty();
                tableBody.empty();

                if (data.length === 0) {
                    tableHeaders.append('<th>No data available</th>');
                    tableBody.append('<tr><td>No records found</td></tr>');
                    return;
                }

                let headers = Object.keys(data[0]);
                headers.push('Actions');
                headers.forEach(header => {
                    tableHeaders.append(`<th>${header}</th>`);
                });

                data.forEach(record => {
                    let row = '<tr>';
                    headers.forEach(header => {
                        if (header !== 'Actions') {
                            row += `<td>${record[header] !== null ? record[header] : ''}</td>`;
                        } else {
                            row +=
                                `<td><i class="restore-btn fas fa-undo" data-id="${record.id}" data-model="${currentFilter}"></i></td>`;
                        }
                    });
                    row += '</tr>';
                    tableBody.append(row);
                });

                $('.restore-btn').click(function() {
                    const id = $(this).data('id');
                    const model = $(this).data('model');
                    restoreRecord(model, id);
                });
            }

            function restoreRecord(model, id) {
                let url = `/api/trash/restore/${model}/${id}`;

                $.ajax({
                    url: url,
                    method: 'PUT',
                    success: function(response) {
                        toastr.success(response.message);
                        fetchTrashData(currentFilter);
                    },
                    error: function() {
                        window.location.href = '/report-error';
                    }
                });
            }

            $('.filter-btn').click(function() {
                const model = $(this).data('filter');
                currentFilter = model;

                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                fetchTrashData(model);
            });

            fetchTrashData(currentFilter);
        });
    </script>
@endsection
