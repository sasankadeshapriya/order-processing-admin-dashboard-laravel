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
                                    <input type="text" class="form-control" id="routeName" name="route_name" value="{{ $route->name }}" placeholder="Enter route name"> 
                                    <div class="invalid-feedback d-none" id="error-route_name"></div>
                                </div>
                                <div id="map" style="height: 58vh;"></div>
                                <input type="hidden" name="waypoints" id="waypointsField" />
                                <input type="hidden" name="added_by_admin_id" value="1" />
                                <button type="submit" class="btn btn-primary mt-3">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> 
                                    Update Route
                                </button>
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
    let startMarker;
    let clientLocations = []; // Array to hold client locations

    function initMap() {
        const mapOptions = {
            zoom: 7,
            center: { lat: 7.8731, lng: 80.7718 },
            minZoom: 6,
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
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            draggable: true,
            polylineOptions: {
                strokeColor: '#FF0000', // Set polyline color to red
                strokeOpacity: 1.0,
                strokeWeight: 4
            }
        });

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
            routePoints.pop(); // Remove the last point

            if (routePoints.length === 0) {
                if (startMarker) {
                    startMarker.setMap(null); // Remove start marker if no points left
                }
                directionsRenderer.setDirections({ routes: [] }); // Clear the route
            } else if (routePoints.length === 1) {
                // If only one point is left, set it as the start marker and clear the route
                if (startMarker) {
                    startMarker.setMap(null);
                }
                startMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(routePoints[0].latitude, routePoints[0].longitude),
                    map: map,
                    title: 'Start Point',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#FF0000',
                        fillOpacity: 1,
                        strokeWeight: 1,
                        strokeColor: '#FFFFFF',
                        scale: 10
                    }
                });
                directionsRenderer.setDirections({ routes: [] });
            } else {
                renderRoute(); // Recalculate and display the route
            }
        }
    }

    function clearWaypoints() {
        routePoints = [];
        if (startMarker) {
            startMarker.setMap(null); // Remove start marker
        }
        directionsRenderer.setDirections({ routes: [] });
    }

    function renderRoute() {
        if (routePoints.length === 1) {
            const firstPoint = routePoints[0];
            if (!startMarker) {
                startMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(firstPoint.latitude, firstPoint.longitude),
                    map: map,
                    title: 'Start Point',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#FF0000',
                        fillOpacity: 1,
                        strokeWeight: 1,
                        strokeColor: '#FFFFFF',
                        scale: 10
                    }
                });
            } else {
                startMarker.setPosition(new google.maps.LatLng(firstPoint.latitude, firstPoint.longitude));
                startMarker.setMap(map);
            }
            directionsRenderer.setDirections({ routes: [] });
            return;
        } else if (routePoints.length < 2) {
            directionsRenderer.setDirections({ routes: [] });
            return;
        }

        const waypoints = routePoints.map(p => ({
            location: new google.maps.LatLng(p.latitude, p.longitude),
            stopover: true
        }));

        const origin = waypoints.shift().location;
        const destination = waypoints.pop().location;

        // Create start marker if it doesn't exist
        if (!startMarker) {
            startMarker = new google.maps.Marker({
                position: origin,
                map: map,
                title: 'Start Point',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#FF0000',
                    fillOpacity: 1,
                    strokeWeight: 1,
                    strokeColor: '#FFFFFF',
                    scale: 10
                }
            });
        } else {
            startMarker.setPosition(origin); // Update position if marker already exists
        }

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.DRIVING,
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                focusOnPolyline(response);
            } else {
                console.error('Failed to render directions due to:', status);
            }
        });
    }

    function focusOnPolyline(response) {
        const bounds = new google.maps.LatLngBounds();
        const route = response.routes[0].overview_path;
        for (let i = 0; i < route.length; i++) {
            bounds.extend(route[i]);
        }
        map.fitBounds(bounds);
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

            const infoWindow = new google.maps.InfoWindow({
                content: `<strong>${client.organization_name}</strong><br>Lat: ${client.latitude}, Lng: ${client.longitude}`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
    const updateRouteForm = document.getElementById('updateRouteForm');
    const submitButton = updateRouteForm.querySelector('button[type="submit"]');
    const routeNameInput = document.getElementById('routeName');
    const spinner = submitButton.querySelector('.spinner-border');
    let initialFormData = getFormDataAsJson(updateRouteForm);
    let initialWaypoints = JSON.stringify(routePoints);

    updateRouteForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentFormData = getFormDataAsJson(updateRouteForm);

        if (JSON.stringify(routePoints) === initialWaypoints && initialFormData === currentFormData) {
            toastr.info('No changes detected. Please modify the data before updating.');
            return;
        }

        // Validate route name
        const routeName = routeNameInput.value.trim();
        let hasError = false;

        if (!routeName) {
            routeNameInput.classList.add('is-invalid');
            document.getElementById('error-route_name').textContent = 'Route name is required.';
            document.getElementById('error-route_name').classList.remove('d-none');
            hasError = true;
        } else {
            routeNameInput.classList.remove('is-invalid');
            document.getElementById('error-route_name').classList.add('d-none');
        }

        if (routePoints.length < 2) {
            toastr.error('Please add at least two waypoints to update the route.');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        document.getElementById('waypointsField').value = JSON.stringify(routePoints);

        spinner.classList.remove('d-none');
        submitButton.disabled = true;

        fetch(updateRouteForm.action, {
            method: 'POST',
            body: new FormData(updateRouteForm)
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Route successfully updated.');
                initialFormData = currentFormData;
                initialWaypoints = JSON.stringify(routePoints); // Update initial waypoints
            } else {
                toastr.error(data.message || 'Failed to update route.');
            }
        }).catch(error => {
            toastr.error('Error: ' + error.message);
        }).finally(() => {
            spinner.classList.add('d-none');
            submitButton.disabled = false;
        });
    });

    function getFormDataAsJson(form) {
        const formData = new FormData(form);
        const formObject = {};
        formData.forEach((value, key) => formObject[key] = value);
        return JSON.stringify(formObject);
    }

    document.getElementById('undoButton').addEventListener('click', undoLastWaypoint);
    document.getElementById('clearButton').addEventListener('click', clearWaypoints);
});

</script>
@endsection
