@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Assignment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Edit Assignment</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <form method="POST" action="{{ route('assignment.update', $assignment['id']) }}" id="editAssignmentForm">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="employee_id">Employee</label>
                                        <select class="form-control" name="employee_id">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee['id'] }}" {{ $assignment['employee_id'] == $employee['id'] ? 'selected' : '' }}>{{ $employee['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="vehicle_id">Vehicle</label>
                                        <select class="form-control" name="vehicle_id">
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle['id'] }}" {{ $assignment['vehicle_id'] == $vehicle['id'] ? 'selected' : '' }}>{{ $vehicle['vehicle_no'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="route_id">Route</label>
                                        <select class="form-control" name="route_id">
                                            @foreach ($routes as $route)
                                                <option value="{{ $route['id'] }}" {{ $assignment['route_id'] == $route['id'] ? 'selected' : '' }}>{{ $route['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="assign_date">Assign Date</label>
                                        <input type="date" class="form-control" name="assign_date" value="{{ old('assign_date', $assignment['assign_date']) }}" style="width: 200px;">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                                    <a href="{{ route('assignment.manage') }}" class="btn btn-secondary">Back to Assignments</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script>
    $(document).ready(function() {
        var initialFormData = $('#editAssignmentForm').serialize();

        $('#editAssignmentForm').submit(function(event) {
            event.preventDefault();
            var currentFormData = $(this).serialize();

            if (initialFormData !== currentFormData) {
                $('#submitBtn').prop('disabled', true).text('Updating...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'PUT',  // Change to PUT since this is an update
                    data: currentFormData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                            // Display field errors if present
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).removeClass('d-none').text(value[0]);
                                });
                            }
                        }
                        $('#submitBtn').prop('disabled', false).text('Update Assignment');
                    },
                    error: function(xhr) {
                        var errorMessage = 'Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText);
                        toastr.error(errorMessage);
                        $('#submitBtn').prop('disabled', false).text('Update Assignment');
                    }
                });
            } else {
                toastr.info('No changes detected. Please modify the data before updating.');
            }
        });
    });
</script>
@endsection

