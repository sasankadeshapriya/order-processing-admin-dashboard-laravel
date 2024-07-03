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
                                    <div class="row"> 
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="employee_id">Employee</label>
                                                <select class="form-control" name="employee_id" id="employee_id">
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee['id'] }}" {{ $assignment['employee_id'] == $employee['id'] ? 'selected' : '' }}>{{ $employee['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-employee_id"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">                                  
                                            <div class="form-group">
                                                <label for="vehicle_id">Vehicle</label>
                                                <select class="form-control" name="vehicle_id" id="vehicle_id">
                                                    @foreach ($vehicles as $vehicle)
                                                        <option value="{{ $vehicle['id'] }}" {{ $assignment['vehicle_id'] == $vehicle['id'] ? 'selected' : '' }}>{{ $vehicle['vehicle_no'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-vehicle_id"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="route_id">Route</label>
                                                <select class="form-control" name="route_id" id="route_id">
                                                    @foreach ($routes as $route)
                                                        <option value="{{ $route['id'] }}" {{ $assignment['route_id'] == $route['id'] ? 'selected' : '' }}>{{ $route['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-route_id"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="assign_date">Assign Date</label>
                                                <input type="date" class="form-control" id="assign_date" name="assign_date" value="{{ old('assign_date', $assignment['assign_date']) }}">
                                                <div class="invalid-feedback d-none" id="error-assign_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Update Assignment</button>
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
        // Set min attribute for assign_date to today's date
        var today = new Date().toISOString().split('T')[0];
        $('#assign_date').attr('min', today);

        var initialFormData = $('#editAssignmentForm').serialize();
        var $submitBtn = $('#submitBtn'); // Declare a variable for the submit button

        $('#editAssignmentForm').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Clear all previous validation errors
            $('.invalid-feedback').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');

            // Client-side validation
            var assignDate = $('[name="assign_date"]').val();
            var employeeId = $('[name="employee_id"]').val();
            var vehicleId = $('[name="vehicle_id"]').val();
            var routeId = $('[name="route_id"]').val();

            var errors = {};

            if (!assignDate) {
                errors.assign_date = ["The assign date field is required."];
            }
            if (!employeeId) {
                errors.employee_id = ["The employee field is required."];
            }
            if (!vehicleId) {
                errors.vehicle_id = ["The vehicle field is required."];
            }
            if (!routeId) {
                errors.route_id = ["The route field is required."];
            }

            if (Object.keys(errors).length > 0) {
                $.each(errors, function(key, value) {
                    $('#error-' + key).removeClass('d-none').text(value[0]);
                    $('[name="' + key + '"]').addClass('is-invalid');
                });
                $submitBtn.prop('disabled', false).text('Update Assignment'); // Re-enable the submit button
                return; // Stop the form submission if there are validation errors
            }

            // If there are no validation errors, submit the form via AJAX
            $submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
            );

            var currentFormData = $(this).serialize();

            $.ajax({
    url: $(this).attr('action'),
    type: 'PUT', // Change to PUT since this is an update
    data: currentFormData,
    success: function(response) {
    console.log('AJAX success callback executed');
    if (response.success) {
        toastr.success(response.message);
        initialFormData = currentFormData; // Update the initial form data to current
        $submitBtn.prop('disabled', false).text('Update Assignment'); // Re-enable the submit button
    } else {
        alert(response.message);
        // Display field errors if present
        if (response.errors && typeof response.errors === 'object' && Object.keys(response.errors).length > 0) {
            $.each(response.errors, function(key, value) {
                $('#error-' + key).removeClass('d-none').text(value[0]);
            });
        }
        $submitBtn.prop('disabled', false).text('Update Assignment'); // Re-enable the submit button
    }
},
    error: function(xhr) {
        console.log('AJAX error callback executed');
        var errorMessage = 'Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText);
        toastr.error(errorMessage);
        $submitBtn.prop('disabled', false).text('Update Assignment'); // Re-enable the submit button
    }
});
        });
    });
</script>
@endsection
