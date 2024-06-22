@extends('layouts.app')

@section('title', 'Update Route')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Route</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Manage Routes</li>
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
                            <form id="updateRouteForm" action="{{ route('route.update', $route->id) }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="routeName">Route Name:</label>
                                    <input type="text" class="form-control" id="routeName" name="route_name" value="{{ $route->name }}" required>
                                </div>
                                <div id="map" style="height: 58vh;"></div>
                                <input type="hidden" name="waypoints" id="waypointsField" />
                                <input type="hidden" name="added_by_admin_id" value="1" />
                                <button type="submit" class="btn btn-primary mt-3">Update Route</button>
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
    let map, directionsService, directionsRenderer;
    let routePoints = JSON.parse(@json($route->waypoints));
    let clientLocations = []; // Array to hold client locations

    function initMap() {
        const mapOptions = {
            zoom: 7,
            center: { lat: 7.8731, lng: 80.7718 },
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({map: map, draggable: true});

        renderRoute();

        map.addListener('click', function(event) {
            addWaypoint(event.latLng);
        });

        // Fetch and render client locations
        fetchClientLocations();
    }

    function addWaypoint(latLng) {
        routePoints.push({ latitude: latLng.lat(), longitude: latLng.lng() });
        renderRoute();
    }

    function undoLastWaypoint() {
        if (routePoints.length > 0) {
            routePoints.pop();
            renderRoute();
        }
    }

    function clearWaypoints() {
        routePoints = [];
        renderRoute();
    }

    function renderRoute() {
        if (routePoints.length < 2) {
            directionsRenderer.setDirections({ routes: [] });
            return;
        }

        const waypoints = routePoints.map(p => ({
            location: new google.maps.LatLng(p.latitude, p.longitude),
            stopover: true
        }));

        const origin = waypoints.shift().location;
        const destination = waypoints.pop().location;

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            } else {
                console.error('Failed to render directions due to:', status);
            }
        });
    }

    function fetchClientLocations() {
        fetch('{{ route('getClientsByRoute', ['routeId' => $route->id]) }}')
            .then(response => response.json())
            .then(data => {
                clientLocations = data;
                renderClientMarkers();
            })
            .catch(error => {
                toastr.error('No client locations.');
                console.error('Error fetching client locations:', error);
            });
    }

    function renderClientMarkers() {
        clientLocations.forEach(client => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(client.latitude), lng: parseFloat(client.longitude) },
                map: map,
                icon: {
                    url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent('<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.30496" cx="19" cy="19" r="19" fill="#B8860B"/><circle cx="19" cy="19" r="15" fill="#B8860B"/><path d="M25.5 14H12.5C11.9477 14 11.5 14.4477 11.5 15V23C11.5 23.5523 11.9477 24 12.5 24H25.5C26.0523 24 26.5 23.5523 26.5 23V15C26.5 14.4477 26.0523 14 25.5 14Z" fill="white" stroke="white" stroke-width="1.3"/><path d="M14 14V11C14 10.4477 14.4477 10 15 10H23C23.5523 10 24 10.4477 24 11V14" stroke="white" stroke-width="1.3"/><path d="M18 20H20V22H18V20Z" fill="#B8860B"/><path d="M22 16H24V18H22V16ZM14 16H16V18H14V16ZM18 16H20V18H18V16ZM14 20H16V22H14V20ZM22 20H24V22H22V20Z" fill="#B8860B"/></svg>')
                },
                title: client.organization_name
            });

            // Optional: Add info window to marker
            const infoWindow = new google.maps.InfoWindow({
                content: `<strong>${client.organization_name}</strong><br>Lat: ${client.latitude}, Lng: ${client.longitude}`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        });
    }

    document.getElementById('updateRouteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (routePoints.length < 2) {
            toastr.error('Please add at least two waypoints to update the route.');
            return;
        }

        document.getElementById('waypointsField').value = JSON.stringify(routePoints);
        var formData = new FormData(this);

        fetch($(this).attr('action'), {
            method: 'POST',
            body: formData
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Route successfully updated.');
            } else {
                toastr.error(data.message || 'Failed to update route.');
            }
        }).catch(error => {
            toastr.error('Error: ' + error.message);
        });
    });

    document.getElementById('undoButton').addEventListener('click', undoLastWaypoint);
    document.getElementById('clearButton').addEventListener('click', clearWaypoints);
</script>
@endsection


