@extends('layouts.app')

@section('title', 'Vehicle Inventory')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Vehicle Inventory</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Vehicle Inventory</li>
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
                                <a href="#" class="btn btn-primary">Add Vehicle Inventory</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Vehicle No</th>
                                                <th>Vehicle Name</th>
                                                <th>Assign Date</th>
                                                <th>Employee Name</th>
                                                <th>Route Name</th>
                                                <th>Products</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedInventories as $vehicleNo => $dateGroups)
                                                @foreach ($dateGroups as $date => $items)
                                                    <tr>
                                                        <td>{{ $vehicleNo }}</td>
                                                        <td>{{ $items->first()['Assignment']['Vehicle']['name'] }}</td>
                                                        <td>{{ $date }}</td>
                                                        <td>{{ $items->first()['Assignment']['Employee']['name'] }}</td>
                                                        <td>{{ $items->first()['Assignment']['Route']['name'] }}</td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm toggle-details">Toggle
                                                                Products</button>
                                                            <div class="details" style="display: none;">
                                                                <table class="table" id="table2">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Name</th>
                                                                            <th>Code</th>
                                                                            <th>SKU</th>
                                                                            <th>Unit</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($items as $key => $item)
                                                                            <tr>
                                                                                <td>{{ $item['Product']['name'] }}</td>
                                                                                <td> {{ $item['sku'] }}</td>
                                                                                <td> {{ $item['quantity'] }}</td>
                                                                                <td>{{ $item['Product']['measurement_unit'] }}
                                                                                </td>
                                                                                <td>
                                                                                    <a href="{{ route('vehicle-inventory.edit', $item['id']) }}"
                                                                                        class="btn btn-secondary btn-sm">
                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                    </a>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm delete-vehicle-inventory"
                                                                                        data-id="{{ $item['id'] }}">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-group"
                                                                    data-id="{{ $vehicleNo }}">
                                                                    <i class="fas fa-trash-alt"></i> Delete Group
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@section('scripts')
    <script src="{{ asset('js/vehicle-inventory-actions.js') }}"></script>
    <script>
        document.querySelectorAll('.toggle-details').forEach(button => {
            button.addEventListener('click', function() {
                const detailsDiv = this.nextElementSibling;
                detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' : 'none';
            });
        });
    </script>
@endsection
@endsection
