@extends('layouts.app')

@section('title', 'Assignments')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Assignments</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Manage Assignments</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('assignment.add') }}" class="btn btn-primary">
                                    Add Assignment <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee Name</th>
                                            <th>Vehicle Number</th>
                                            <th>Route Name</th>
                                            <th>Assign Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $key => $assignment)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $assignment['employee_name'] }}</td>
                                                <td>{{ $assignment['vehicle_number'] }}</td>
                                                <td>{{ $assignment['route_name'] }}</td>
                                                <td>{{ $assignment['assign_date'] }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('assignment.edit', $assignment['id']) }}"
                                                            class="btn btn-secondary btn-sm mr-2">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm delete-assignment"
                                                            data-id="{{ $assignment['id'] }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee Name</th>
                                            <th>Vehicle Number</th>
                                            <th>Route Name</th>
                                            <th>Assign Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/assignment-action.js') }}"></script>
    <!-- DataTables & Plugins -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            if (!$.fn.dataTable.isDataTable('#example1')) {
                var table = $('#example1').DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    order: [
                        [4, 'desc']
                    ], // Default sorting on the Assign Date column
                    columnDefs: [{
                            targets: 1,
                            type: 'string'
                        }, // Employee Name
                        {
                            targets: 4,
                            type: 'date'
                        } // Assign Date
                    ]
                });

                table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            }
        });
    </script>
@endsection
