@extends('layouts.app')

@section('title', 'Today\'s Assignments')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Today's Assignments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Today's Assignments</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Vehicle Number</th>
                                        <th>Route Name</th>
                                        <th>Assign Date</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assignments as $key => $assignment)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $assignment['employee_name'] }}</td>
                                        <td>{{ $assignment['vehicle_number'] }}</td>
                                        <td>{{ $assignment['route_name'] }}</td>
                                        <td>{{ $assignment['assign_date'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm track-location" data-id="{{ $assignment['id'] }}" data-waypoints="{{ json_encode($assignment['waypoints']) }}">
                                                <i class="fas fa-map-marker-alt"></i> Location
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Route Map</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="popupMap" style="height: 400px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}" async defer></script>
<script>
$(document).ready(function() {
    let map;
    let marker;
    let employeeId;
    let locationInterval;

    $('.track-location').click(function() {
        var waypointsJson = $(this).attr('data-waypoints');
        employeeId = $(this).attr('data-id');
        try {
            var waypoints = JSON.parse(waypointsJson);
            if (waypoints && waypoints.length > 0) {
                $('#mapModal').modal('show');
                $('#mapModal').on('shown.bs.modal', function() {
                    initMapModal(waypoints, employeeId);
                });
            } else {
                console.error('No waypoints found for this assignment.');
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
        }
    });

    $('#mapModal').on('hidden.bs.modal', function() {
        clearInterval(locationInterval);
        // Clear map and markers when modal is closed
        if (map) {
            map = null;
        }
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        if (directionsRenderer) {
            directionsRenderer.setMap(null);
            directionsRenderer = null;
        }
        console.log('Stopped fetching location updates.');
    });

    function initMapModal(waypoints, employeeId) {
        if (locationInterval) {
            clearInterval(locationInterval);
        }

        var mapOptions = {
            zoom: 12,
            center: new google.maps.LatLng(waypoints[0].latitude, waypoints[0].longitude),
        };

        // Clear map and markers before initializing
        if (map) {
            map = null;
        }
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        if (directionsRenderer) {
            directionsRenderer.setMap(null);
            directionsRenderer = null;
        }

        map = new google.maps.Map(document.getElementById('popupMap'), mapOptions);
        marker = new google.maps.Marker({
            map: map,
            icon: {
                url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent('<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.30496" cx="19" cy="19" r="19" fill="#EC8E37"/><circle cx="19" cy="19" r="15" fill="#EC3737"/><path fill-rule="evenodd" clip-rule="evenodd" d="M25 17.7273C25 22.1818 19 26 19 26C19 26 13 22.1818 13 17.7273C13 14.5642 15.6863 12 19 12C22.3137 12 25 14.5642 25 17.7273V17.7273Z" fill="white" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><ellipse cx="19" cy="17.7274" rx="2" ry="1.90909" fill="#EC3737" stroke="#EC8E37" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>')
            }
        });

        var directionsService = new google.maps.DirectionsService();
        var directionsRenderer = new google.maps.DirectionsRenderer({
            draggable: false,
            map: map,
            suppressMarkers: true
        });
        calculateAndDisplayRoute(directionsService, directionsRenderer, waypoints);

        updateEmployeeLocation(employeeId);
        locationInterval = setInterval(function() {
            updateEmployeeLocation(employeeId);
        }, 10000);
    }

    function calculateAndDisplayRoute(directionsService, directionsRenderer, waypoints) {
        var waypts = waypoints.map(waypoint => ({
            location: new google.maps.LatLng(waypoint.latitude, waypoint.longitude),
            stopover: true
        }));

        var origin = waypts[0].location;
        var destination = waypts[waypts.length - 1].location;
        waypts.shift();
        waypts.pop();

        directionsService.route({
            origin: origin,
            destination: destination,
            waypoints: waypts,
            travelMode: 'DRIVING'
        }, function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);

                new google.maps.Marker({
                    position: origin,
                    icon: {
                        url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent('<svg width="50" height="50" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.30496" cx="19" cy="19" r="19" fill="#EA352B"/><circle cx="19" cy="19" r="15" fill="#EA352B"/><path d="M8.128 21.549C7.80133 21.549 7.50733 21.493 7.246 21.381C6.98467 21.2643 6.77933 21.101 6.63 20.891C6.48067 20.681 6.406 20.436 6.406 20.156H7.26C7.27867 20.366 7.36033 20.5387 7.505 20.674C7.65433 20.8093 7.862 20.877 8.128 20.877C8.40333 20.877 8.618 20.8117 8.772 20.681C8.926 20.5457 9.003 20.373 9.003 20.163C9.003 19.9997 8.954 19.8667 8.856 19.764C8.76267 19.6613 8.64367 19.582 8.499 19.526C8.359 19.47 8.163 19.4093 7.911 19.344C7.59367 19.26 7.33467 19.176 7.134 19.092C6.938 19.0033 6.77 18.868 6.63 18.686C6.49 18.504 6.42 18.2613 6.42 17.958C6.42 17.678 6.49 17.433 6.63 17.223C6.77 17.013 6.966 16.852 7.218 16.74C7.47 16.628 7.76167 16.572 8.093 16.572C8.56433 16.572 8.94933 16.691 9.248 16.929C9.55133 17.1623 9.71933 17.4843 9.752 17.895H8.87C8.856 17.7177 8.772 17.566 8.618 17.44C8.464 17.314 8.261 17.251 8.009 17.251C7.78033 17.251 7.59367 17.3093 7.449 17.426C7.30433 17.5427 7.232 17.7107 7.232 17.93C7.232 18.0793 7.27633 18.203 7.365 18.301C7.45833 18.3943 7.575 18.469 7.715 18.525C7.855 18.581 8.04633 18.6417 8.289 18.707C8.611 18.7957 8.87233 18.8843 9.073 18.973C9.27833 19.0617 9.451 19.1993 9.591 19.386C9.73567 19.568 9.808 19.813 9.808 20.121C9.808 20.3683 9.74033 20.6017 9.605 20.821C9.47433 21.0403 9.28067 21.2177 9.024 21.353C8.772 21.4837 8.47333 21.549 8.128 21.549ZM14.8926 16.635V17.286H13.5976V21.5H12.7996V17.286H11.4976V16.635H14.8926ZM19.6338 20.506H17.5968L17.2468 21.5H16.4138L18.1568 16.628H19.0808L20.8238 21.5H19.9838L19.6338 20.506ZM19.4098 19.855L18.6188 17.594L17.8208 19.855H19.4098ZM25.1086 21.5L23.9886 19.554H23.3796V21.5H22.5816V16.635H24.2616C24.635 16.635 24.95 16.7003 25.2066 16.831C25.468 16.9617 25.6616 17.1367 25.7876 17.356C25.9183 17.5753 25.9836 17.8203 25.9836 18.091C25.9836 18.4083 25.8903 18.6977 25.7036 18.959C25.5216 19.2157 25.2393 19.3907 24.8566 19.484L26.0606 21.5H25.1086ZM23.3796 18.917H24.2616C24.5603 18.917 24.7843 18.8423 24.9336 18.693C25.0876 18.5437 25.1646 18.343 25.1646 18.091C25.1646 17.839 25.09 17.643 24.9406 17.503C24.7913 17.3583 24.565 17.286 24.2616 17.286H23.3796V18.917ZM31.1475 16.635V17.286H29.8525V21.5H29.0545V17.286H27.7525V16.635H31.1475Z" fill="white"/></svg>')
                    },
                    map: map
                });

                new google.maps.Marker({
                    position: destination,
                    icon: {
                        url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent('<svg width="50" height="50" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg"><circle opacity="0.30496" cx="19" cy="19" r="19" fill="#EA352B"/><circle cx="19" cy="19" r="15" fill="#EA352B"/><path d="M12.323 17.279V18.707H14.003V19.358H12.323V20.849H14.213V21.5H11.525V16.628H14.213V17.279H12.323ZM20.1929 21.5H19.3949L16.9939 17.867V21.5H16.1959V16.628H16.9939L19.3949 20.254V16.628H20.1929V21.5ZM23.8298 16.635C24.3478 16.635 24.8005 16.7353 25.1878 16.936C25.5798 17.132 25.8808 17.4167 26.0908 17.79C26.3055 18.1587 26.4128 18.5903 26.4128 19.085C26.4128 19.5797 26.3055 20.009 26.0908 20.373C25.8808 20.737 25.5798 21.017 25.1878 21.213C24.8005 21.4043 24.3478 21.5 23.8298 21.5H22.2408V16.635H23.8298ZM23.8298 20.849C24.3992 20.849 24.8355 20.695 25.1388 20.387C25.4422 20.079 25.5938 19.645 25.5938 19.085C25.5938 18.5203 25.4422 18.0793 25.1388 17.762C24.8355 17.4447 24.3992 17.286 23.8298 17.286H23.0388V20.849H23.8298Z" fill="white"/></svg>')
                    },
                    map: map
                });
            } else {
                console.error('Directions request failed due to ' + status);
            }
        });
    }

    function updateEmployeeLocation(employeeId) {
        console.log(`Fetching location for employee ID: ${employeeId}`);
        $.get(`/employee/${employeeId}/location`, function(data) {
            if (data && data.location) {
                console.log(`Current location: Latitude ${data.location.latitude}, Longitude ${data.location.longitude}`);
                const latLng = new google.maps.LatLng(data.location.latitude, data.location.longitude);
                map.setCenter(latLng);
                marker.setPosition(latLng);
            } else {
                console.error('Failed to get location data');
            }
        }).fail(function(err) {
            console.error('Error fetching employee location:', err);
        });
    }
});

</script>
@endsection
