@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Vehicle</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Edit Vehicle</li>
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
                            <form method="POST" action="{{ route('vehicle.update', $vehicle->id) }}"
                                  enctype="multipart/form-data" id="editVehicleForm">
                                @csrf
                                @method('PUT')
                                <div class="card-body"> 
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Number</label>
                                                <input type="text" class="form-control" name="vehicle_number"  placeholder="AB-1234 / ABC-1234 / 12-1234"
                                                       id="vehicle_no"
                                                       value="{{ isset($vehicle->vehicle_no) ? $vehicle->vehicle_no : '' }}">
                                                <div class="invalid-feedback d-none" id="error-vehicle_number"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Name</label>
                                                <input type="text" class="form-control" name="vehicle_name" placeholder="Delivery Truck"
                                                       value="{{ $vehicle->name }}">
                                                <div class="invalid-feedback d-none" id="error-vehicle_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Model</label>
                                                <select class="form-control" name="vehicle_model">
                                                    <option value="Lorry" {{ $vehicle->type == 'Lorry' ? 'selected' : '' }}>Lorry</option>
                                                    <option value="Van" {{ $vehicle->type == 'Van' ? 'selected' : '' }}>Van</option>
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-vehicle_model"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">Update Vehicle</button>
                                    <a href="{{ route('vehicle.manage') }}" class="btn btn-secondary">Back to Vehicles</a>
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
    <script>
        $(document).ready(function() {
            var initialFormData = $('#editVehicleForm').serialize();

            // Transform the vehicle number to uppercase
            $('#vehicle_no').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            $('#editVehicleForm').submit(function(event) {
                event.preventDefault();
                var currentFormData = $(this).serialize();
                var $submitBtn = $('#submitBtn');
                var hasErrors = false;

                // Clear all previous validation errors
                $('.invalid-feedback').addClass('d-none').text('');
                $('.form-control').removeClass('is-invalid');

                // Check if required fields are empty
                $('#editVehicleForm').find('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        var fieldName = $(this).attr('name');
                        $('#error-' + fieldName).removeClass('d-none').text('This field is required');
                        $(this).addClass('is-invalid');
                        hasErrors = true;
                    }
                });

                // Front-end validation for vehicle number only if the field is not empty
                const vehicleNo = $('#vehicle_no').val();
                if (vehicleNo && !/^(?:[A-Za-z]{2}-\d{4}|[A-Za-z]{3}-\d{4}|\d{2}-\d{4})$/.test(vehicleNo)) {
                    $('#error-vehicle_number').removeClass('d-none').text('Invalid vehicle number format');
                    $('#vehicle_no').addClass('is-invalid');
                    hasErrors = true;
                }

                if (hasErrors) {
                    $submitBtn.prop('disabled', false).html('Update Vehicle');
                    return;
                }

                // Check if the form data has changed
                if (initialFormData === currentFormData) {
                    toastr.info('No changes detected. Please modify the data before updating.');
                    $submitBtn.prop('disabled', false).html('Update Vehicle');
                    return;
                }

                // If there are no validation errors, submit the form via AJAX
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                );

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST', // Assuming the form uses POST for updates
                    data: currentFormData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            initialFormData = currentFormData; // Update the initial form data to current
                        } else {
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).removeClass('d-none').text(value[0]);
                                    $('[name="' + key + '"]').addClass('is-invalid');
                                });
                            } else {
                                toastr.error(response.message || 'Failed to update vehicle');
                            }
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText));
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html('Update Vehicle');
                    }
                });
            });
        });
    </script>
@endsection
