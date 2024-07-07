@extends('layouts.app')

@section('title', 'Client')

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
                            <li class="breadcrumb-item active">Manage Client</li>
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
                            <div class="card-header">
                                <a href="{{ route('client.add') }}" class="btn btn-primary">
                                    Add Client <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Organization</th>
                                                <th>Location</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($clients as $key => $client)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $client['name'] ?? 'N/A' }}</td>
                                                    <td>{{ $client['organization_name'] ?? 'N/A' }}</td>
                                                    <td data-lat="{{ $client['latitude'] ?? 'null' }}"
                                                        data-lng="{{ $client['longitude'] ?? 'null' }}">
                                                        <button type="button" class="btn btn-info btn-sm view-map-btn">View
                                                            Map</button>
                                                    </td>
                                                    <td>{{ $client['phone_no'] ?? 'N/A' }}</td>
                                                    <td>{{ $client['status'] ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('client.edit', $client['id']) }}"
                                                                class="btn btn-secondary btn-sm mr-2">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-client"
                                                                data-id="{{ $client['id'] }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Organization</th>
                                                <th>Location</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
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
                    <h5 class="modal-title" id="mapModalLabel">Client Location</h5>
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}" async defer></script>
    <script>
        $(document).ready(function() {
            $('.view-map-btn').on('click', function() {
                var lat = $(this).closest('td').data('lat');
                var lng = $(this).closest('td').data('lng');

                $('#mapModal').modal('show');
                $('#mapModal').on('shown.bs.modal', function() {
                    initMapModal(lat, lng);
                });
            });
        });

        function initMapModal(lat, lng) {
            const modalBody = document.querySelector('#mapModal .modal-body');
            if (lat == null || lng == null) {
                modalBody.innerHTML = '<p class="text-center text-danger">Location data not available.</p>';
                modalBody.style.height = '400px'; // keep the height consistent
            } else {
                modalBody.innerHTML = '<div id="popupMap" style="height: 400px;"></div>';
                const location = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                };
                const mapOptions = {
                    zoom: 15,
                    center: location
                };

                var map = new google.maps.Map(document.getElementById('popupMap'), mapOptions);

                new google.maps.Marker({
                    position: location,
                    map: map
                });
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=&v=weekly" async
        defer></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
@endsection
