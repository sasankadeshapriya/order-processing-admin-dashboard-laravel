@extends('layouts.app')

@section('title', 'Edit Vehicle Inventory')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Vehicle Inventory</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Edit Vehicle Inventory</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <form method="POST" action="{{ route('vehicle-inventory.update', ['id' => $inventory['id']]) }}"
                        id="vehicleInventoryForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Assignment ID</label>
                                        <input type="number" class="form-control" name="assignment_id"
                                            placeholder="Enter Assignment ID" value="{{ $inventory['assignment_id'] }}"
                                            disabled>
                                        <input type="hidden" name="assignment_id"
                                            value="{{ $inventory['assignment_id'] }}">
                                        <div class="invalid-feedback d-none" id="error-assignment_id">Assignment ID is
                                            required.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Product and SKU</label>
                                        <select class="form-control" name="product_id" id="productSelect" disabled>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                                    data-max-quantity="{{ $product['quantity'] }}"
                                                    @if ($product['id'] == $inventory['product_id']) selected @endif>
                                                    {{ $product['name'] }} - {{ $product['sku'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="product_id" value="{{ $inventory['product_id'] }}">
                                        <input type="hidden" name="sku" value="{{ $inventory['sku'] }}">
                                        <div class="invalid-feedback d-none" id="error-product_id">Please select a product.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" step="0.5" class="form-control" name="quantity"
                                            id="quantityInput" placeholder="Enter Quantity"
                                            value="{{ $inventory['quantity'] }}">
                                        <small id="availableQuantity" class="form-text text-muted"></small>
                                        <div class="invalid-feedback d-none" id="error-quantity">Please enter a valid
                                            quantity.</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="added_by_admin_id" value="{{ $inventory['added_by_admin_id'] }}">
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Update</button>
                            <a href="{{ route('vehicle.inventory') }}" class="btn btn-secondary">Back to Inventory</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var initialData = {
                assignment_id: $('#assignment_id').val(),
                product_id: $('#productSelect').val(),
                quantity: $('#quantityInput').val()
            };

            $('#productSelect').change(updateProductDetails);

            $('#vehicleInventoryForm').submit(function(event) {
                event.preventDefault();

                if (!dataHasChanged()) {
                    toastr.info('No changes detected. Please update before submit.');
                    return;
                }

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                );
                clearValidation();

                let formData = new FormData(this);
                formData.set('_method', 'PUT'); // Ensure method is PUT for processing
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST', // Use POST but override with _method: PUT
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        toastr.success(response.message);
                        $('#availableQuantity').text('Available quantity: ' + response
                            .availableQuantity).show();
                        $('#quantityInput').attr('max', response.maxQuantity);
                        $('#submitBtn').prop('disabled', false).html('Update');
                        updateInitialData();
                        clearValidation();
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                        $('#submitBtn').prop('disabled', false).html('Update');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#error-' + key).removeClass('d-none').text(value[0]);
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        }
                    }
                });
            });

            function updateProductDetails() {
                let selectedOption = $('#productSelect option:selected');
                let maxQuantity = parseFloat(selectedOption.attr('data-max-quantity'));
                let currentQuantity = parseFloat($('#quantityInput').val());
                let newMaxQuantity = maxQuantity + currentQuantity; // Sum of available batch and current quantity
                $('#quantityInput').attr('max', newMaxQuantity);
                $('#availableQuantity').text('Available quantity: ' + maxQuantity).show();
            }

            function clearValidation() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').addClass('d-none').empty();
            }

            function updateInitialData() {
                initialData.assignment_id = $('#assignment_id').val();
                initialData.product_id = $('#productSelect').val();
                initialData.quantity = $('#quantityInput').val();
            }

            function dataHasChanged() {
                return $('#assignment_id').val() !== initialData.assignment_id ||
                    $('#productSelect').val() !== initialData.product_id ||
                    $('#quantityInput').val() !== initialData.quantity;
            }

            updateProductDetails(); // Set initial values
        });
    </script>
@endsection
