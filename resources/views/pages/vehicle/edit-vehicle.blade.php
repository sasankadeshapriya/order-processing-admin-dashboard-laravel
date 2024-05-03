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
                                                <input type="text" class="form-control" name="vehicle_number"
                                                    value="{{ isset($vehicle->vehicle_no) ? $vehicle->vehicle_no : '' }}">
                                                <div class="invalid-feedback d-none" id="error-vehicle_number"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Name</label>
                                                <input type="text" class="form-control" name="vehicle_name"
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
                                                    <option value="Lorry"
                                                        {{ $vehicle->type == 'Lorry' ? 'selected' : '' }}>Lorry
                                                    </option>
                                                    <option value="Van"
                                                        {{ $vehicle->type == 'Van' ? 'selected' : '' }}>Van
                                                    </option>
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-vehicle_model"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">Update Vehicle</button>
                                    <a href="{{ route('vehicle.manage') }}" class="btn btn-secondary">Back to
                                        Vehicles</a>
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

            $('#editVehicleForm').submit(function(event) {
                event.preventDefault();
                var currentFormData = $(this).serialize();

                // Check if form data has changed
                if (initialFormData !== currentFormData) {
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: currentFormData,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                            } else {
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#error-' + key).removeClass('d-none').text(value[0]);
                                    });
                                } else {
                                    toastr.error(response.message || 'Failed to update vehicle');
                                }
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                        },
                        complete: function() {
                            $('#submitBtn').prop('disabled', false).html('Update Vehicle');
                        }
                    });
                } else {
                    toastr.info('No changes detected. Please modify the data before updating.');
                }
            });
        });
    </script>
@endsection
