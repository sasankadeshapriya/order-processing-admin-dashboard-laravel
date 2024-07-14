@extends('layouts.app')

@section('title', 'Employee')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Employee</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Manage Employee</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('employee.add') }}" class="btn btn-primary">
                                    Add Employee <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>NIC</th>
                                                <th>Phone</th>
                                                <th>Commission Rate<small> (%)</small></th>
                                                <th>Profile Picture</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employees as $key => $employee)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $employee['name'] ?? 'N/A' }}</td>
                                                    <td>{{ $employee['email'] ?? 'N/A' }}</td>
                                                    <td>{{ $employee['nic'] ?? 'N/A' }}</td>
                                                    <td>{{ $employee['phone_no'] ?? 'N/A' }}</td>
                                                    <td contenteditable="true" class="editable-cell" onBlur="updateCommissionRate(this)" data-employee-id="{{ $employee['id'] }}">
                                                        {{ number_format($employee['commission_rate'], 2) ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if ($employee['profile_picture'])
                                                            <a href="javascript:void(0);" onclick="openImagePopup('{{ $employee['profile_picture'] }}')">
                                                                <img src="{{ $employee['profile_picture'] }}" alt="profile image" width="30px" height="30px">
                                                            </a>
                                                        @else
                                                            No Image
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm delete-employee" data-id="{{ $employee['id'] }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>NIC</th>
                                                <th>Phone</th>
                                                <th>Commission Rate<small> (%)</small></th>
                                                <th>Profile Picture</th>
                                                <th>Action</th>
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
    <script src="{{ asset('js/employee-action.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function () {
            if (!$.fn.dataTable.isDataTable('#example1')) {
                $('#example1').DataTable({
                    "order": [[0, "asc"]],
                    "dom": 'Bfrtip',
                    "buttons": [
                        {
                            extend: 'excelHtml5',
                            text: 'Export to Excel',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Export to PDF',
                            className: 'btn btn-danger'
                        },
                        {
                            extend: 'colvis',
                            text: 'Column Visibility',
                            className: 'btn btn-info'
                        }
                    ]
                });
            }
        });

        function openImagePopup(imageUrl) {
            var popup = window.open('', '_blank');
            var img = popup.document.createElement('img');
            img.src = imageUrl;
            img.style.width = '100%';
            popup.document.body.appendChild(img);
        }

        function updateCommissionRate(element) {
            const commissionRate = element.innerText.trim();
            const employeeId = element.getAttribute('data-employee-id');

            if (!isNaN(commissionRate) && commissionRate !== '') {
                const commissionRateFloat = parseFloat(commissionRate);
                if (commissionRateFloat < 0 || commissionRateFloat > 100) {
                    toastr.error('Commission rate must be between 0 and 100');
                    element.classList.add('error-cell');
                    return;
                }

                fetch(`/employee/update-commission/${employeeId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ commission_rate: commissionRateFloat }),
                    mode: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        element.innerText = commissionRateFloat.toFixed(2);
                        element.classList.remove('error-cell');
                        toastr.success('Commission rate updated successfully');
                    } else {
                        toastr.error('Failed to update commission rate: ' + (data.message || 'Unknown error'));
                        element.classList.add('error-cell');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error updating commission rate');
                    element.classList.add('error-cell');
                });
            } else {
                toastr.error('Invalid commission rate');
                element.classList.add('error-cell');
            }
        }
    </script>
@endsection

<style>
.editable-cell {
    background-color: #e0f7fa; /* Light blue background for editable cells */
    border: 1px solid #007bff; /* Blue border to indicate editability */
    cursor: pointer; /* Pointer cursor to indicate the cell is clickable */
}

.editable-cell:focus {
    outline: none; /* Remove default outline on focus */
}

.error-cell {
    background-color: #f8d7da; /* Light red background for error cells */
    border: 1px solid #dc3545; /* Red border for error cells */
}
</style>