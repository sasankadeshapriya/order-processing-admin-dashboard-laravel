@extends('layouts.app')

@section('title', 'Add Vehicle Inventory')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Vehicle Inventory</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Add Vehicle Inventory</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <form method="POST" action="{{ route('vehicle-inventory.submit') }}" id="vehicleInventoryForm">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Assignment ID</label>
                                        <input type="number" step="" class="form-control" name="assignment_id"
                                            placeholder="Enter Assignment ID">
                                        <div class="invalid-feedback d-none" id="error-assignment_id">Assignment ID is
                                            required.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Product and SKU</label>
                                        <select class="form-control" name="product_id" id="productSelect">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product['id'] }}" data-sku="{{ $product['sku'] }}"
                                                    data-max-quantity="{{ $product['quantity'] }}">
                                                    {{ $product['name'] }} - {{ $product['sku'] }}
                                                </option>
                                            @endforeach
                                        </select>
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
                                            id="quantityInput" placeholder="Enter Quantity">
                                        <small id="availableQuantity" class="form-text text-muted"></small>
                                        <div class="invalid-feedback d-none" id="error-quantity">Please enter a valid
                                            quantity.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="added_by_admin_id" value="1">
                            <input type="hidden" name="sku" id="skuInput"> <!-- Hidden input for SKU -->
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('vehicle.inventory') }}" class="btn btn-secondary">Back to
                                Inventory</a>
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
            $('#productSelect').change(updateProductDetails);

            $('#vehicleInventoryForm').submit(function(event) {
                event.preventDefault();
                clearValidation();

                let formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        toastr.success(response.message);
                        $('#vehicleInventoryForm')[0].reset();
                        $('#availableQuantity').text('')
                            .hide(); // Hide the available quantity text
                        clearValidation();

                        if (response.success && response.products) {
                            updateDropdown(response.products);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#error-' + key).removeClass('d-none').text(value[0]);
                                $('[name="' + key + '"]').addClass('is-invalid');
                            });
                        }
                    }
                });
            });

            function updateDropdown(products) {
                let productSelect = $('#productSelect');
                productSelect.empty(); // Clear existing options
                productSelect.append($('<option>', {
                    value: '',
                    text: 'Select Product'
                }));
                $.each(products, function(index, product) {
                    productSelect.append($('<option>', {
                        value: product.id,
                        text: product.name + ' - ' + product.sku,
                        'data-sku': product.sku,
                        'data-max-quantity': product.quantity
                    }));
                });
            }

            function updateProductDetails() {
                let selectedOption = $('#productSelect option:selected');
                let maxQuantity = selectedOption.attr('data-max-quantity');
                let sku = selectedOption.attr('data-sku');
                $('#quantityInput').attr('max', maxQuantity);
                $('#skuInput').val(sku);
                $('#availableQuantity').text('Available quantity: ' + maxQuantity)
                    .show(); // Show and update the available quantity text
            }

            function clearValidation() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').addClass('d-none').empty();
            }
        });
    </script>
@endsection
