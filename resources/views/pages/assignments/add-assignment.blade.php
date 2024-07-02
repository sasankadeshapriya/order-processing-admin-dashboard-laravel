@extends('layouts.app')

@section('title', 'Add Assignment')

@section('content')
    <div class="content-wrapper"> 
        <section class="content-header"> 
            <div class="container-fluid"> 
                <div class="row mb-2"> 
                    <div class="col-sm-6"> 
                        <h1>Add Assignment</h1> 
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
                            <form method="POST" action="{{ route('assignment.submit') }}" enctype="multipart/form-data" id="assignmentForm">
                                @csrf 
                                <div class="card-body">
                                    <div class="row"> 
                                        <div class="col-sm-6">
                                            <div class="form-group"> 
                                                <label for="employee_id">Employee</label> 
                                                <select class="form-control" name="employee_id" id="employee_id"> 
                                                    <option value="">Select Employee</option> 
                                                    @foreach ($employees as $employee) 
                                                        <option value="{{ $employee['id'] }}">{{ $employee['name'] }}</option> 
                                                    @endforeach 
                                                </select> 
                                                <div class="invalid-feedback d-none" id="error-employee_id"></div>
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group"> 
                                                <label for="vehicle_id">Vehicle</label> 
                                                <select class="form-control" name="vehicle_id" id="vehicle_id"> 
                                                    <option value="">Select Vehicle</option> 
                                                    @foreach ($vehicles as $vehicle)
                                                        <option value="{{ $vehicle['id'] }}">{{ $vehicle['vehicle_no'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-vehicle_id"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="route_id">Route</label>
                                                <select class="form-control" name="route_id" id="route_id">
                                                    <option value="">Select Route</option>
                                                    @foreach ($routes as $route)
                                                        <option value="{{ $route['id'] }}">{{ $route['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-route_id"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="assign_date">Assign Date</label>
                                                <input type="date" class="form-control" id="assign_date" name="assign_date">
                                                <div class="invalid-feedback d-none" id="error-assign_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Assignment</button>
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
    <script>
        $(document).ready(function() {
            // Set min attribute for assign_date to today's date
            var today = new Date().toISOString().split('T')[0];
            $('#assign_date').attr('min', today);

            $('#assignmentForm').submit(function(event) {
                event.preventDefault(); // Prevent the form from submitting via the browser.
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                );

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
                    $('#submitBtn').prop('disabled', false).text('Submit Assignment');
                } else {
                    var formData = $(this).serialize(); // Serialize the form data

                    $.ajax({
                        url: '{{ route('assignment.submit') }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Assignment added successfully!');
                                $('#assignmentForm')[0].reset(); // Clear all form fields after successful submission
                            } else {
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#error-' + key).removeClass('d-none').text(value[0]);
                                        $('[name="' + key + '"]').addClass('is-invalid');
                                    });
                                } else {
                                    alert('Error adding assignment: ' + (response.message || "An unknown error occurred."));
                                }
                            }
                            $('#submitBtn').prop('disabled', false).text('Submit Assignment'); // Re-enable submit button
                        },
                        error: function(xhr) {
                            var errorMessage = 'An unexpected error occurred. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            alert('Error adding assignment: ' + errorMessage);
                            $('#submitBtn').prop('disabled', false).text('Submit Assignment'); // Re-enable submit button
                        }
                    });
                }
            });
        });
    </script>
@endsection
