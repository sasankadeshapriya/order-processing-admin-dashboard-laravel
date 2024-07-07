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
                                <a href="{{ route('vehicle-inventory.add') }}" class="btn btn-primary">Add Vehicle
                                    Inventory</a>
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
                                                    @php
                                                        $locked = collect($items)->contains(
                                                            fn($item) => $item['looked'] == 1,
                                                        );
                                                    @endphp
                                                    <tr class="{{ $locked ? 'table-danger' : '' }}">
                                                        <td>{{ $vehicleNo }}</td>
                                                        <td>{{ $items->first()['Assignment']['Vehicle']['name'] ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $date }}</td>
                                                        <td>{{ $items->first()['Assignment']['Employee']['name'] ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $items->first()['Assignment']['Route']['name'] ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-info btn-sm toggle-details">Toggle
                                                                Products</button>
                                                            <div class="details" style="display: none;">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Name</th>
                                                                            <th>SKU</th>
                                                                            <th>Qty</th>
                                                                            <th>Unit</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($items as $key => $item)
                                                                            <tr>
                                                                                <td>{{ $item['Product']['name'] ?? 'N/A' }}
                                                                                </td>
                                                                                <td>{{ $item['sku'] ?? 'N/A' }}</td>
                                                                                <td>{{ $item['quantity'] ?? 'N/A' }}</td>
                                                                                <td>{{ $item['Product']['measurement_unit'] ?? 'N/A' }}
                                                                                </td>
                                                                                <td>
                                                                                    <a href="{{ route('vehicle-inventory.edit', $item['id']) }}"
                                                                                        class="btn btn-secondary btn-sm edit-link {{ $locked ? 'disabled-link' : '' }}"
                                                                                        onclick="return {{ $locked ? 'false' : 'true' }};">
                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                    </a>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm delete-vehicle-inventory {{ $locked ? 'disabled' : '' }}"
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
                                                                    class="btn btn-{{ $locked ? 'secondary' : 'warning' }} btn-sm toggle-looked"
                                                                    data-ids="{{ implode(',', $items->pluck('id')->toArray()) }}"
                                                                    data-lock="{{ $locked ? 'false' : 'true' }}">
                                                                    <i
                                                                        class="fas fa-{{ $locked ? 'lock-open' : 'lock' }}"></i>
                                                                    {{ $locked ? 'Unlock' : 'Lock' }}
                                                                </button>
                                                                &nbsp;
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-group {{ $locked ? 'disabled' : '' }}"
                                                                    data-ids="{{ implode(',', $items->pluck('id')->toArray()) }}">
                                                                    <i class="fas fa-trash-alt"></i> Delete Group
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Vehicle No</th>
                                                <th>Vehicle Name</th>
                                                <th>Assign Date</th>
                                                <th>Employee Name</th>
                                                <th>Route Name</th>
                                                <th>Products</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/vehicle-inventory-actions.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-details').forEach(button => {
                button.addEventListener('click', function() {
                    const detailsDiv = this.nextElementSibling;
                    detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' :
                        'none';
                });
            });

            document.querySelectorAll('.edit-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    if (this.classList.contains('disabled-link')) {
                        event.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.toggle-looked').forEach(button => {
                button.addEventListener('click', function() {
                    const groupRow = $(this).closest('tr');
                    const groupRows = groupRow.find('.details tbody tr');
                    var ids = groupRows.map(function() {
                        return $(this).find('.delete-vehicle-inventory').data('id');
                    }).get();

                    const isLock = $(this).data('lock') === 'false';

                    Swal.fire({
                        title: `Are you sure you want to ${isLock ? 'unlock' : 'lock'} these items?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#C8B400',
                        cancelButtonColor: '#d33',
                        confirmButtonText: `Yes, ${isLock ? 'unlock' : 'lock'} them!`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/api/proxy/vehicle-inventory/toggle-looked/${ids.join(',')}`,
                                type: 'PUT',
                                contentType: 'application/json',
                                success: function(response) {
                                    toastr.success(
                                        `Items successfully ${isLock ? 'unlocked' : 'locked'}.`
                                    );
                                    groupRows.each(function() {
                                        $(this).find(
                                            '.btn-secondary, .btn-danger'
                                        ).prop('disabled', !
                                            isLock);
                                    });
                                    $(button).data('lock', isLock.toString());
                                    $(button).find('i').toggleClass(
                                        'fa-lock fa-lock-open');
                                    $(button).toggleClass(
                                        'btn-secondary btn-warning');
                                    $(groupRow).find('.delete-group').prop(
                                        'disabled', !isLock);
                                },
                                error: function(xhr, status, error) {
                                    toastr.error(
                                        `Failed to ${isLock ? 'unlock' : 'lock'} items: ${xhr.responseText}`
                                    );
                                }
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
