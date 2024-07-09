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
                                <h6>
                                    <span class="bg-dangerr">
                                        AQty: available quantity && IQty: initial quentity
                                    </span>
                                </h6>
                                <h6>
                                    <span class="bg-dangery">
                                        The lock icon is used to secure the vehicle inventory and mark it as ready. Once
                                        locked, administrators will not be able to edit or delete the inventory.
                                    </span>
                                </h6>
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
                                                    <tr class="group-row {{ $locked ? 'table-danger' : '' }}"
                                                        data-locked="{{ $locked ? 'true' : 'false' }}">
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
                                                                            <th>AQty</th>
                                                                            <th>IQty</th>
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
                                                                                <td>{{ $item['intialqty'] ?? 'N/A' }}</td>
                                                                                <td>{{ $item['Product']['measurement_unit'] ?? 'N/A' }}
                                                                                </td>
                                                                                <td>
                                                                                    <a href="{{ route('vehicle-inventory.edit', $item['id']) }}"
                                                                                        class="btn btn-secondary btn-sm edit-link {{ $locked ? 'disabled-link' : '' }}">
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
                                                                    <i class="fas fa-trash-alt"></i>
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
            // Toggle details visibility
            document.querySelectorAll('.toggle-details').forEach(button => {
                button.addEventListener('click', function() {
                    const detailsDiv = this.nextElementSibling;
                    detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' :
                        'none';
                });
            });

            // Prevent default action for disabled edit links
            document.querySelectorAll('.edit-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    if (this.classList.contains('disabled-link')) {
                        event.preventDefault();
                    }
                });
            });

            // Update button states based on lock status
            function updateButtonStates() {
                document.querySelectorAll('.group-row').forEach(row => {
                    const isLocked = row.dataset.locked === 'true';
                    const toggleButton = row.querySelector('.toggle-looked');
                    const groupRows = row.querySelectorAll('.details tbody tr');

                    groupRows.forEach(subRow => {
                        const editLink = subRow.querySelector('.edit-link');
                        const deleteButton = subRow.querySelector('.delete-vehicle-inventory');

                        if (editLink) {
                            editLink.classList.toggle('disabled-link', isLocked);
                        }

                        if (deleteButton) {
                            deleteButton.disabled = isLocked;
                            deleteButton.classList.toggle('disabled', isLocked);
                        }
                    });

                    const deleteGroupButton = row.querySelector('.delete-group');
                    if (deleteGroupButton) {
                        deleteGroupButton.disabled = isLocked;
                        deleteGroupButton.classList.toggle('disabled', isLocked);
                    }

                    if (toggleButton) {
                        toggleButton.dataset.lock = isLocked ? 'false' : 'true';
                        toggleButton.classList.toggle('btn-warning', !isLocked);
                        toggleButton.classList.toggle('btn-secondary', isLocked);
                        toggleButton.querySelector('i').className = isLocked ? 'fas fa-lock' :
                            'fas fa-lock-open';
                        row.classList.toggle('table-danger', isLocked);
                    }
                });
            }

            // Initial update on page load
            updateButtonStates();

            // Toggle lock state for each vehicle inventory one by one
            document.querySelectorAll('.toggle-looked').forEach(button => {
                button.addEventListener('click', function() {
                    const groupRow = this.closest('tr');
                    const groupRows = groupRow.querySelectorAll('.details tbody tr');
                    let ids = Array.from(groupRows).map(row => row.querySelector(
                        '.delete-vehicle-inventory').dataset.id);

                    const isCurrentlyLocked = groupRow.dataset.locked === 'true';

                    function toggleLockState(index) {
                        if (index >= ids.length) {
                            toastr.success(
                                `All items successfully ${isCurrentlyLocked ? 'unlocked' : 'locked'}.`
                            );
                            groupRow.dataset.locked = isCurrentlyLocked ? 'false' : 'true';
                            updateButtonStates();
                            return;
                        }

                        $.ajax({
                            url: `/api/proxy/vehicle-inventory/toggle-looked/${ids[index]}`,
                            type: 'PUT',
                            contentType: 'application/json',
                            success: function(response) {
                                toggleLockState(index + 1);
                            },
                            error: function(xhr, status, error) {
                                toastr.error(
                                    `Failed to ${isCurrentlyLocked ? 'unlock' : 'lock'} item with ID ${ids[index]}: ${xhr.responseText}`
                                );
                                toggleLockState(index + 1);
                            }
                        });
                    }

                    Swal.fire({
                        title: `Are you sure you want to ${isCurrentlyLocked ? 'unlock' : 'lock'} these items?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#C8B400',
                        cancelButtonColor: '#d33',
                        confirmButtonText: `Yes, ${isCurrentlyLocked ? 'unlock' : 'lock'} them!`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            toggleLockState(0); // Start processing from the first ID
                        }
                    });
                });
            });
        });
    </script>
@endsection
