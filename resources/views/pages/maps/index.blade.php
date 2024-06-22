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
    let clientMarkers = [];

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
        map = new google.maps.Map(document.getElementById('map'), mapOptions); // Initialize the map
        directionsService = new google.maps.DirectionsService(); // Initialize the directions service
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            draggable: true
        }); // Initialize the directions renderer with draggable routes

        map.addListener('click', function(event) {
            addRoutePoint(event.latLng); // Add route point on map click
        });
        fetchClientLocations();
    }
    function fetchClientLocations() {
        $.ajax({
            url: '{{ route('client.locations') }}', // Assuming you have a route to fetch client locations
            type: 'GET',
            success: function(data) {
                if (data.success) {
                    displayClientLocations(data.clients); // Display client locations on the map
                } else {
                    toastr.error(data.message || 'Failed to fetch client locations.');
                }
            },
            error: function(xhr) {
                var errMsg = xhr.status + ': ' + xhr.statusText;
                toastr.error('Error - ' + errMsg); // Show error message
            }
        });
    }

    function displayClientLocations(clients) {
    clients.forEach(function(client) {
        if (client.latitude && client.longitude) {
            var clientLatLng = new google.maps.LatLng(parseFloat(client.latitude), parseFloat(client.longitude));
            var marker = new google.maps.Marker({
                position: clientLatLng,
                map: map,
                title: client.name,
                icon: {
    url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent('<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.30496" cx="19" cy="19" r="19" fill="#B8860B"/><circle cx="19" cy="19" r="15" fill="#B8860B"/><path d="M25.5 14H12.5C11.9477 14 11.5 14.4477 11.5 15V23C11.5 23.5523 11.9477 24 12.5 24H25.5C26.0523 24 26.5 23.5523 26.5 23V15C26.5 14.4477 26.0523 14 25.5 14Z" fill="white" stroke="white" stroke-width="1.3"/><path d="M14 14V11C14 10.4477 14.4477 10 15 10H23C23.5523 10 24 10.4477 24 11V14" stroke="white" stroke-width="1.3"/><path d="M18 20H20V22H18V20Z" fill="#B8860B"/><path d="M22 16H24V18H22V16ZM14 16H16V18H14V16ZM18 16H20V18H18V16ZM14 20H16V22H14V20ZM22 20H24V22H22V20Z" fill="#B8860B"/></svg>')
}
            });
            clientMarkers.push(marker); // Add marker to array
        }
    });
}

    function addRoutePoint(location) {
        console.log("Clicked location: ", location.toString());
        routePoints.push(location); // Add clicked location to route points
        if (routePoints.length === 1) {
            if (startMarker) {
                startMarker.setMap(null); // Remove existing start marker
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
            }); // Create and set the start marker
        } else {
            calculateAndDisplayRoute(); // Calculate and display route if there are multiple points
        }
    }

    function calculateAndDisplayRoute() {
        if (routePoints.length < 2) return; // Exit if there are fewer than 2 points
        const waypoints = routePoints.slice(1, -1).map(location => ({ location: location, stopover: true })); // Prepare waypoints
        const origin = routePoints[0]; // Set origin
        const destination = routePoints[routePoints.length - 1]; // Set destination

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response); // Display the route
            } else {
                console.error('Directions request failed due to:', status); // Log error
            }
        });
    }

    function undoLastWaypoint() {
        const now = Date.now();
        if (now - lastClickTime < 250) return; // Ignore clicks that are too close in time
        lastClickTime = now;
        if (routePoints.length > 0) {
            routePoints.pop(); // Remove the last point
            if (routePoints.length === 0) {
                if (startMarker) {
                    startMarker.setMap(null); // Remove start marker if no points left
                }
                directionsRenderer.setDirections({ routes: [] }); // Clear the route
            } else {
                calculateAndDisplayRoute(); // Recalculate and display the route
            }
        }
    }

    function clearWaypoints() {
        routePoints = []; // Clear all points
        if (startMarker) {
            startMarker.setMap(null); // Remove the start marker
        }
        directionsRenderer.setDirections({ routes: [] }); // Clear the displayed route
    }

    $('#addRouteForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        // Check if there are less than 2 waypoints
        if (routePoints.length < 2) {
            toastr.error('Please add at least two waypoints to create a route.'); // Show error message
            return; // Prevent form submission
        }

        var formData = new FormData(this);
        formData.append('waypoints', JSON.stringify(routePoints.map(p => ({ latitude: p.lat(), longitude: p.lng() })))); // Append waypoints to form data
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
                    toastr.success(data.message || 'Route successfully created.'); // Show success message
                } else {
                    toastr.error(data.message || 'Failed to create route.'); // Show error message
                }
            },
            error: function(xhr) {
                var errMsg = xhr.status + ': ' + xhr.statusText;
                toastr.error('Error - ' + errMsg); // Show error message
            }
        });
    });

    document.getElementById('undoButton').addEventListener('click', undoLastWaypoint); // Add event listener for undo button
    document.getElementById('clearButton').addEventListener('click', clearWaypoints); // Add event listener for clear button

</script>
@endsection
