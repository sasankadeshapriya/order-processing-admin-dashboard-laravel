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
