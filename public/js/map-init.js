let map;
let startMarker;
let directionsService;
let directionsRenderer;
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
    map = new google.maps.Map(document.getElementById("map"), mapOptions);

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    map.addListener("click", addRoutePoint);

    document.getElementById("undoButton").addEventListener("click", undoLastWaypoint);
}

function addRoutePoint(event) {
    const location = event.latLng;
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
        // Always calculate route from the start point to the last point with intermediate waypoints
        calculateAndDisplayRoute(routePoints[0], routePoints[routePoints.length - 1], routePoints.slice(1, -1));
    }
}

function undoLastWaypoint() {
    const now = Date.now();
    if (now - lastClickTime < 250) return; // Ignore clicks that are too close
    lastClickTime = now;

    console.log("Undo button clicked.");
    console.log("Undo clicked, current number of waypoints: ", routePoints.length);

    if (routePoints.length > 0) {
        routePoints.pop();
        console.log("Waypoint removed, new number of waypoints: ", routePoints.length);

        if (routePoints.length === 0) {
            startMarker.setMap(null);
            directionsRenderer.setDirections({ routes: [] });
            console.log("All waypoints and markers cleared.");
        } else {
            if (routePoints.length === 1) {
                startMarker.setPosition(routePoints[0]);
                directionsRenderer.setDirections({ routes: [] });
                console.log("Only start marker reset, no routes to display.");
            } else {
                calculateAndDisplayRoute(routePoints[0], routePoints[routePoints.length - 1], routePoints.slice(1, -1));
                console.log("Route recalculated for remaining waypoints.");
            }
        }
    } else {
        window.alert("No waypoints to remove.");
    }
}

function clearWaypoints() {
    if (routePoints.length === 0) {
        window.alert("There are no waypoints to clear.");
        return;
    }

    routePoints = [];
    if (startMarker) {
        startMarker.setMap(null);
    }
    directionsRenderer.setDirections({ routes: [] });
}


function calculateAndDisplayRoute(origin, destination, waypoints) {
    const formattedWaypoints = waypoints.map(location => ({location: location, stopover: true}));

    directionsService.route({
        origin: origin,
        destination: destination,
        waypoints: formattedWaypoints,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(response);
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}
