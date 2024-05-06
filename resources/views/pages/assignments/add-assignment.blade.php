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
                        <div class="card-body">
                            <form id="assignmentForm" action="{{ route('assignment.submit') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="employee_id">Employee</label>
                                    <select class="form-control" name="employee_id">
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="vehicle_id">Vehicle</label>
                                    <select class="form-control" name="vehicle_id">
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle['id'] }}">{{ $vehicle['vehicle_no'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="route_id">Route</label>
                                    <select class="form-control" name="route_id">
                                        @foreach ($routes as $route)
                                            <option value="{{ $route['id'] }}">{{ $route['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="assign_date">Assign Date</label>
                                    <input type="date" class="form-control" id="assign_date" name="assign_date" style="width: 200px">
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
        </div>
    </section>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#assignmentForm').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting via the browser.
            $('#submitBtn').prop('disabled', true).text('Submitting...'); // Disable submit button

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                url: '{{ route('assignment.submit') }}', // Adjust this to the correct URL to your route
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Assignment added successfully!');
                        $('#assignmentForm')[0].reset(); // Clear all form fields after successful submission
                    } else {
                        // Display error message directly from the server
                        alert('Error adding assignment: ' + (response.message || "An unknown error occurred."));
                    }
                    $('#submitBtn').prop('disabled', false).text('Submit Assignment'); // Re-enable submit button
                },
                error: function(xhr) {
                    // Default error message if the server response does not include one
                    var errorMessage = 'An unexpected error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        // Display error message from server response
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert('Error adding assignment: ' + errorMessage);
                    $('#submitBtn').prop('disabled', false).text('Submit Assignment'); // Re-enable submit button
                }
            });
        });
    });
</script>
@endsection



