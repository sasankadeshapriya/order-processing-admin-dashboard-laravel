@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Client</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Edit Client</li>
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
                        <form method="POST" action="{{ route('client.update', $client->id) }}" id="clientForm">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ $client->name }}" placeholder="Enter client's name">
                                            <div class="invalid-feedback d-none" id="error-name"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Organization Name</label>
                                            <input type="text" class="form-control" name="organization_name" value="{{ $client->organization_name }}" placeholder="Enter organization's name">
                                            <div class="invalid-feedback d-none" id="error-organization_name"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone_no" id="phone_no" value="{{ $client->phone_no }}" placeholder="Enter phone number (+1234567890)">
                                            <div class="invalid-feedback d-none" id="error-phone_no"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status">
                                                <option value="">Select Status</option>
                                                <option value="verified" {{ $client->status == 'verified' ? 'selected' : '' }}>Verified</option>
                                                <option value="not verified" {{ $client->status == 'not verified' ? 'selected' : '' }}>Not Verified</option>
                                            </select>
                                            <div class="invalid-feedback d-none" id="error-status"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Discount<small> (%)</small></label>
                                            <input type="number" step="0.1" min="0" max="100" class="form-control" name="discount" value="{{ $client->discount }}" placeholder="Enter discount percentage">
                                            <div class="invalid-feedback d-none" id="error-discount"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Credit Limit<small> (in LKR format)</small></label>
                                            <input type="number" step="1" min="0" class="form-control" name="credit_limit" value="{{ $client->credit_limit }}" placeholder="Enter credit limit">
                                            <div class="invalid-feedback d-none" id="error-credit_limit"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Credit Period<small> (Days)</small></label>
                                            <input type="number" min="1" class="form-control" name="credit_period" value="{{ $client->credit_period }}" placeholder="Enter credit period in days">
                                            <div class="invalid-feedback d-none" id="error-credit_period"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Route</label>
                                            <select class="form-control" name="route_id" id="route_id">
                                                <option value="">Select Route</option>
                                                @foreach ($routes as $route)
                                                    <option value="{{ $route['id'] }}" {{ $client->route_id == $route['id'] ? 'selected' : '' }}>{{ $route['name'] }}</option>
                                                @endforeach
                                                @if (empty($routes))
                                                    <option value="" disabled>No routes available</option>
                                                @endif
                                            </select>
                                            <div class="invalid-feedback d-none" id="error-route_id"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <div class="input-group">
                                                @php
                                                    $locationValue = ($client->latitude && $client->longitude) ? "{$client->latitude}, {$client->longitude}" : "Select location";
                                                @endphp
                                                <input type="text" class="form-control" name="location" id="location" value="{{ $locationValue }}" readonly>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#locationModal">Select Location</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ $client->latitude }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ $client->longitude }}">
                                    <input type="hidden" name="added_by_employee_id" value="123">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="submitBtn">Update Client</button>
                                <a href="{{ route('client.manage') }}" class="btn btn-secondary">Back to Clients</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Select Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="clearLocationBtn">Clear Location</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
<script>
    let selectedLat = {{ $client->latitude ?? 'null' }};
    let selectedLng = {{ $client->longitude ?? 'null' }};
    let map, marker;

    function initMap() {
  const defaultLocation = { lat: 7.8731, lng: 80.7718 };
  const clientLocation = (selectedLat !== null && selectedLng !== null) ? { lat: selectedLat, lng: selectedLng } : defaultLocation;

  const mapOptions = {
    center: clientLocation,
    zoom: 8,
    restriction: {
      latLngBounds: {
        north: 10.2,
        south: 5.7,
        east: 82.0,
        west: 79.5,
      },
      strictBounds: false
    }
  };

  map = new google.maps.Map(document.getElementById('map'), mapOptions);

  if (selectedLat !== null && selectedLng !== null) {
    marker = new google.maps.Marker({
      position: clientLocation,
      map: map
    });
  }

  map.addListener('click', function(event) {
    placeMarker(event.latLng);
  });
}
    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
        selectedLat = location.lat();
        selectedLng = location.lng();

        $('#location').val(`${selectedLat}, ${selectedLng}`);
        $('#latitude').val(selectedLat);
        $('#longitude').val(selectedLng);
    }

    $('#locationModal').on('shown.bs.modal', function () {
        google.maps.event.trigger(map, "resize");
        const newCenter = (selectedLat !== null && selectedLng !== null) ? { lat: selectedLat, lng: selectedLng } : { lat: 40.7128, lng: -74.0060 };
        map.setCenter(newCenter);
    });

    $('#clearLocationBtn').click(function() {
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        selectedLat = null;
        selectedLng = null;
        $('#location').val('');
        $('#latitude').val('');
        $('#longitude').val('');
    });

    $(document).ready(function() {
        $('#clientForm input, #clientForm select').each(function() {
            $(this).data('original-value', $(this).val());
        });
    });

    $('#clientForm').submit(function(event) {
        event.preventDefault();
        $('#submitBtn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
        );

        // Clear all previous validation errors
        $('.invalid-feedback').addClass('d-none').text('');
        $('.form-control').removeClass('is-invalid');

        let hasError = false;
        let formChanged = false;

        // Perform custom required validation
        $('#clientForm input, #clientForm select').each(function() {
            if ($(this).val() === '' && $(this).attr('name') !== 'added_by_employee_id' && $(this).attr('name') !== 'latitude' && $(this).attr('name') !== 'longitude' && $(this).attr('name') !== 'location') {
                hasError = true;
                $(this).addClass('is-invalid');
                $('#error-' + $(this).attr('name')).removeClass('d-none').text('This field is required');
            }
            // Check if the form data has changed
            if ($(this).val() !== $(this).data('original-value')) {
                formChanged = true;
            }
        });

        if (hasError) {
            $('#submitBtn').prop('disabled', false).html('Update Client');
            return;
        }

        // Perform client-side validation for phone number
        const phoneNo = $('#phone_no').val().trim();
        const phoneRegex = /^\+?\d{9,13}$/;
        if (!phoneRegex.test(phoneNo)) {
            $('#error-phone_no').removeClass('d-none').text('Please enter a valid phone number');
            $('#phone_no').addClass('is-invalid');
            $('#submitBtn').prop('disabled', false).html('Update Client');
            return;
        }

        if (!formChanged) {
            toastr.info('No changes detected. Please modify the data before updating.');
            $('#submitBtn').prop('disabled', false).html('Update Client');
            return;
        }

        // Prepare form data
        const formData = $(this).serializeArray();
        const formattedData = {};

        // Convert relevant fields to correct types
        formData.forEach(field => {
            if (field.name === 'discount' || field.name === 'credit_limit' || field.name === 'latitude' || field.name === 'longitude') {
                formattedData[field.name] = parseFloat(field.value);
            } else if (field.name === 'credit_period' || field.name === 'route_id' || field.name === 'added_by_employee_id') {
                formattedData[field.name] = parseInt(field.value);
            } else {
                formattedData[field.name] = field.value;
            }
        });

        console.log("Formatted Data: ", formattedData); // Log formatted data

        // Continue with AJAX form submission if validation passes
        $.ajax({
            url: $('#clientForm').attr('action'),
            type: 'PUT',
            contentType: 'application/json',  // Explicitly set the content type
            data: JSON.stringify(formattedData),  // Ensure data is sent as a JSON string
            success: function(response) {
                console.log("Server Response: ", response);
                if (response.success) {
                    let successCode = response.code; // Assuming the success code is in the "code" property
                    console.log("Success Code: ", successCode);
                    toastr.success(response.message);
                    // Do not reset the form and do not remove the marker
                } else {
                    console.log("Response details: ", response);
                    toastr.error(response.message || 'Failed to update client');
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON.message || xhr.statusText;
                console.error("Error Code: ", xhr.status); 
                console.error("Error: ", errorMessage);
                toastr.error('Error: ' + errorMessage);
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('Update Client');
            }
        });
    });
</script>

@endsection
