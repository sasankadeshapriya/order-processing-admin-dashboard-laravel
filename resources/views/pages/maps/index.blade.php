@extends('layouts.app')

@section('title', 'Add Route')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add New Route</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Create Route</li>
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
                            <form id="addRouteForm" action="{{ route('route.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="routeName">Route Name:</label>
                                    <input type="text" class="form-control" id="routeName" name="route_name" placeholder="Enter route name" required>
                                </div>
                                <div id="map" style="height: 58vh;"></div>
                                <input type="hidden" name="waypoints" id="waypointsField" />
                                <input type="hidden" name="added_by_admin_id" value="1">
                                <button type="submit" class="btn btn-primary mt-3">Save Route</button>
                                <button type="button" class="btn btn-secondary mt-3" id="undoButton">Undo</button>
                                <button type="button" class="btn btn-danger mt-3" id="clearButton">Clear All</button>
                                <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='{{ route('route.manage') }}'">Back to Routes</button>
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
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script>
    let map, startMarker, directionsService, directionsRenderer;
    let routePoints = [];
    let lastClickTime = 0;

    function initMap() {
        const sriLankaCenter = { lat: 7.8731, lng: 80.7718 };
        const mapOptions = {
            zoom: 8,
            center: sriLankaCenter,
            minZoom: 6,
            restriction: {
                latLngBounds: {
                    north: 10.2,
                    south: 5.7,
                    east: 82.0,
                    west: 79.5,
                },
                strictBounds: true
            }
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            draggable: true
        });

        map.addListener('click', function(event) {
            addRoutePoint(event.latLng);
        });
    }

    function addRoutePoint(location) {
        console.log("Clicked location: ", location.toString());
        routePoints.push(location);
        if (routePoints.length === 1) {
            if (startMarker) {
                startMarker.setMap(null);
            }
            startMarker = new google.maps.Marker({
                position: location,
                map: map,
                title: 'Start Point',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#FF0000',
                    fillOpacity: 1,
                    strokeWeight: 0,
                    scale: 10
                }
            });
        } else {
            calculateAndDisplayRoute();
        }
    }

    function calculateAndDisplayRoute() {
        if (routePoints.length < 2) return;
        const waypoints = routePoints.slice(1, -1).map(location => ({ location: location, stopover: true }));
        const origin = routePoints[0];
        const destination = routePoints[routePoints.length - 1];

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            } else {
                console.error('Directions request failed due to:', status);
            }
        });
    }

    function undoLastWaypoint() {
        const now = Date.now();
        if (now - lastClickTime < 250) return; // Ignore clicks that are too close
        lastClickTime = now;
        if (routePoints.length > 0) {
            routePoints.pop();
            if (routePoints.length === 0) {
                if (startMarker) {
                    startMarker.setMap(null);
                }
                directionsRenderer.setDirections({ routes: [] });
            } else {
                calculateAndDisplayRoute();
            }
        }
    }

    function clearWaypoints() {
        routePoints = [];
        if (startMarker) {
            startMarker.setMap(null);
        }
        directionsRenderer.setDirections({ routes: [] });
    }

    $('#addRouteForm').submit(function(e) {
    e.preventDefault();

    // Check if there are less than 2 waypoints
    if (routePoints.length < 2) {
        toastr.error('Please add at least two waypoints to create a route.');
        return; // Prevent form submission
    }

    var formData = new FormData(this);
    formData.append('waypoints', JSON.stringify(routePoints.map(p => ({ latitude: p.lat(), longitude: p.lng() }))));
    $.ajax({
        url: this.action,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                toastr.success(data.message || 'Route successfully created.');
            } else {
                toastr.error(data.message || 'Failed to create route.');
            }
        },
        error: function(xhr) {
            var errMsg = xhr.status + ': ' + xhr.statusText;
            toastr.error('Error - ' + errMsg);
        }
    });
});

document.getElementById('undoButton').addEventListener('click', undoLastWaypoint);
document.getElementById('clearButton').addEventListener('click', clearWaypoints);

</script>
@endsection
