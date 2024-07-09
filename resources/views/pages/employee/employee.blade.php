@extends('layouts.app')

@section('title', 'Employee')

@section('content')
    <div class="content-wrapper">
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
                                                    <td>{{ $employee['commission_rate'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($employee['profile_picture'])
                                                            <a href="javascript:void(0);"
                                                                onclick="openImagePopup('{{ $employee['profile_picture'] }}')">
                                                                <img src="{{ $employee['profile_picture'] }}"
                                                                    alt="profile image" width="30px" height="30px">
                                                            </a>
                                                        @else
                                                            No Image
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('employee.edit', $employee['id']) }}"
                                                                class="btn btn-secondary btn-sm mr-2">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-employee"
                                                                data-id="{{ $employee['id'] }}">
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
    <script>
        function openImagePopup(imageUrl) {
            var popup = window.open('', '_blank');
            var img = popup.document.createElement('img');
            img.src = imageUrl;
            img.style.width = '100%';
            popup.document.body.appendChild(img);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-employee').forEach(function(button) {
                button.addEventListener('click', function() {
                    var employeeId = this.getAttribute('data-id');
                    if (confirm('Are you sure you want to delete this employee?')) {
                        // Implement the delete functionality using AJAX or form submission
                    }
                });
            });
        });
    </script>

@endsection
