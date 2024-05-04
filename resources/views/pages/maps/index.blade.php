@include('libraries.style')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Route Planner</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- Assuming you are using Laravel Mix for Tailwind CSS -->
    <style>
        #map {
            height: 83%; /* Use viewport height for better responsiveness */
            width: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .button-container {
            position: absolute;
            bottom: 20px;
            left: 20px;
            display: flex;
            justify-content: flex-start;
            align-items: flex-end;
        }

        .button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            display: inline-block;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 8px;
        }

        /* Adjustments for the route name input */
        .route-name-input {
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="antialiased d-flex flex-column">
    <div class="container-fluid flex-grow-1">
        <div id="map"></div>
    </div>
    <div class="button-container">
        <form id="routeForm">
            <div class="col-sm-6">
                <div class="form-group" style="width: 380">
                    <input type="text" id="routeName" class="form-control" placeholder="Enter route name" name="route_name" required>
                <div class="invalid-feedback d-none" id="error-name"></div>
                </div>
            </div>
            <button type="button" class="button undo-button" onclick="undoLastWaypoint()" id="undoButton" style="background-color: #c8b400">
                <i class="bi bi-arrow-counterclockwise"></i> Undo
            </button>
            <button type="button" class="button custom-button" onclick="clearWaypoints()" id="clearButton" style="background-color: #dc3545">
                <i class="bi bi-x-lg"></i> Clear All
            </button>
            <button type="submit" class="button custom-button" id="saveButton" style="background-color: #28a745">
                <i class="bi bi-save"></i> Save Route
            </button>
        </form>
    </div>

    <!-- Include jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Then include your scripts that use jQuery -->
    <script src="{{ asset('js/map-init.js') }}"></script>

    <script>
$(document).ready(function() {
    $('#routeForm').on('submit', function(e) {
        e.preventDefault();  // Prevents the default form submission
        console.log("Form submission intercepted.");

        const routeName = $('#routeName').val().trim();
        if (!routeName) {
            alert('Please enter a route name.');
            $('#routeName').focus();
            return false;
        }

        if (routePoints.length === 0) {
            alert('No waypoints selected. Please select waypoints on the map.');
            return false;
        }

        // Transform waypoints from {lat, lng} to {latitude, longitude}
        const transformedWaypoints = routePoints.map(point => {
            return { latitude: point.lat(), longitude: point.lng() };  // Call the functions to get values
        });

        console.log("Transformed Waypoints:", transformedWaypoints);

        const formData = new FormData(this);
        formData.append('waypoints', JSON.stringify(transformedWaypoints));
        formData.append('added_by_admin_id', '1');  // Assuming admin ID 1 for example

        $.ajax({
            url: '/submit.route', // Make sure this URL matches your routes in Laravel
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {
                    alert('Route saved successfully!');
                    $('#routeForm')[0].reset(); // Reset the form fields
                    clearWaypoints(); // Assuming clearWaypoints() is already implemented to handle this
                } else {
                    alert('Failed to save route: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed.", xhr.responseText);
                alert('Error: ' + xhr.responseText);
            },
            complete: function() {
                $('#saveButton').prop('disabled', false).html('<i class="bi bi-save"></i> Save Route');
            }
        });
    });
});




    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"></script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
