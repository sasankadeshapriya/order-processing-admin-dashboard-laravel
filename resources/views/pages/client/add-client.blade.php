@extends('layouts.app')

@section('title', 'Add Client')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Client</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Add Client</li>
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
                    <form method="POST" action="{{ route('client.submit') }}" id="clientForm">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" placeholder="Enter client's name">
                                        <div class="invalid-feedback d-none" id="error-name"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Organization Name</label>
                                        <input type="text" class="form-control" name="organization_name" placeholder="Enter organization's name">
                                        <div class="invalid-feedback d-none" id="error-organization_name"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" name="phone_no" id="phone_no" placeholder="Enter phone number (+1234567890)">
                                        <div class="invalid-feedback d-none" id="error-phone_no"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="">Select Status</option>
                                            <option value="verified">Verified</option>
                                            <option value="not verified">Not Verified</option>
                                        </select>
                                        <div class="invalid-feedback d-none" id="error-status"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Discount<small> (%)</small></label>
                                        <input type="number" step="0.1" min="0" max="100" class="form-control" name="discount" placeholder="Enter discount percentage">
                                        <div class="invalid-feedback d-none" id="error-discount"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Credit Limit<small> (in LKR format)</small></label>
                                        <input type="number" step="1" min="0" class="form-control" name="credit_limit" placeholder="Enter credit limit">
                                        <div class="invalid-feedback d-none" id="error-credit_limit"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Credit Period<small> (Days)</small></label>
                                        <input type="number" min="1" class="form-control" name="credit_period" placeholder="Enter credit period in days">
                                        <div class="invalid-feedback d-none" id="error-credit_period"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Route</label>
                                        <select class="form-control" name="route_id" id="route_id">
                                            <option value="">Select Route</option>
                                            @foreach ($routes as $route)
                                                <option value="{{ $route['id'] }}">{{ $route['name'] }}</option>
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
                                            <input type="text" class="form-control" name="location" id="location" placeholder="Latitude, Longitude" readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#locationModal">Select Location</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="hidden" name="added_by_employee_id" value="123">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Client</button>
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
    let selectedLat, selectedLng;
    let map, marker;

    function initMap() {
        const sriLankaCenter = { lat: 7.8731, lng: 80.7718 };
        map = new google.maps.Map(document.getElementById('map'), {
            center: sriLankaCenter,
            zoom: 8
        });

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

        // Update the location field with combined latitude and longitude
        $('#location').val(`${selectedLat}, ${selectedLng}`);
        $('#latitude').val(selectedLat);
        $('#longitude').val(selectedLng);
    }

    $('#locationModal').on('shown.bs.modal', function () {
        google.maps.event.trigger(map, "resize");
        map.setCenter({ lat: 7.8731, lng: 80.7718 });
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

    $('#clientForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        $('#submitBtn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
        );

        // Clear all previous validation errors
        $('.invalid-feedback').addClass('d-none').text('');
        $('.form-control').removeClass('is-invalid');

        // Perform custom required validation
        let hasError = false;

        $('#clientForm input, #clientForm select').each(function() {
            if ($(this).val() === '' && $(this).attr('name') !== 'added_by_employee_id' && $(this).attr('name') !== 'latitude' && $(this).attr('name') !== 'longitude' && $(this).attr('name') !== 'location') {
                hasError = true;
                $(this).addClass('is-invalid');
                $('#error-' + $(this).attr('name')).removeClass('d-none').text('This field is required');
            }
        });

        if (hasError) {
            $('#submitBtn').prop('disabled', false).html('Submit Client');
            return;
        }

        // Perform client-side validation for phone number
        const phoneNo = $('#phone_no').val().trim();
        const phoneRegex = /^\+?\d{9,13}$/;
        if (!phoneRegex.test(phoneNo)) {
            $('#error-phone_no').removeClass('d-none').text('Please enter a valid phone number');
            $('#phone_no').addClass('is-invalid');
            $('#submitBtn').prop('disabled', false).html('Submit Client');
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
            url: '{{ route('client.submit') }}',
            type: 'POST',
            contentType: 'application/json',  // Explicitly set the content type
            data: JSON.stringify(formattedData),  // Ensure data is sent as a JSON string
            success: function(response) {
                console.log("Server Response: ", response);
                if (response.success) {
                    toastr.success(response.message);
                    $('#clientForm')[0].reset();
                    // Clear the marker and reset the location fields
                    if (marker) {
                        marker.setMap(null);
                        marker = null;
                    }
                    selectedLat = null;
                    selectedLng = null;
                    $('#location').val('');
                    $('#latitude').val('');
                    $('#longitude').val('');
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#error-' + key).removeClass('d-none').text(value[0]);
                            $('input[name="' + key + '"], select[name="' + key + '"]').addClass('is-invalid');
                        });
                    } else {
                        toastr.error(response.message || 'Failed to add client');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON.message || xhr.statusText;
                if (xhr.responseJSON.errors) {
                    console.error("Validation Errors: ", xhr.responseJSON.errors);
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMessage += '<br>' + value;
                    });
                }
                console.error("Error: ", errorMessage);
                toastr.error('Error: ' + errorMessage);
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('Submit Client');
            }
        });
    });
</script>
@endsection
