@extends('layouts.app')

@section('title', 'Add Batch')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Batch</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Add Batch</li>
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
                            <form method="POST" action="{{ route('batch.submit') }}" id="batchForm">
                                @csrf
                                <div class="card-body">
                                    <p>All prices in <code>.LKR</code> format.</p>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- SKU -->
                                            <div class="form-group">
                                                <label>SKU</label>
                                                <input type="text" class="form-control" name="sku"
                                                    placeholder="P01#240123">
                                                <div class="invalid-feedback d-none" id="error-sku"></div>
                                            </div>
                                            <!-- Product Selection -->
                                            @if (count($products) > 0)
                                                <div class="form-group">
                                                    <label>Product</label>
                                                    <select class="form-control" name="product_id">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product['id'] }}">{{ $product['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback d-none" id="error-product_id"></div>
                                                </div>
                                            @else
                                                <p>0 products</p>
                                            @endif
                                            <!-- Buy Price -->
                                            <div class="form-group">
                                                <label>Buy Price</label>
                                                <input type="number" step="0.01" class="form-control" name="buy_price"
                                                    placeholder="100.50">
                                                <div class="invalid-feedback d-none" id="error-buy_price"></div>
                                            </div>
                                            <!-- Cash Price -->
                                            <div class="form-group">
                                                <label>Cash Price</label>
                                                <input type="number" step="0.01" class="form-control" name="cash_price"
                                                    placeholder="105.50">
                                                <div class="invalid-feedback d-none" id="error-cash_price"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <!-- Check Price -->
                                            <div class="form-group">
                                                <label>Cheque Price</label>
                                                <input type="number" step="0.01" class="form-control" name="check_price"
                                                    placeholder="110.50">
                                                <div class="invalid-feedback d-none" id="error-check_price"></div>
                                            </div>
                                            <!-- Credit Price -->
                                            <div class="form-group">
                                                <label>Credit Price</label>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="credit_price" placeholder="115.50">
                                                <div class="invalid-feedback d-none" id="error-credit_price"></div>
                                            </div>
                                            <!-- Quantity -->
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" step="0.5" class="form-control" name="quantity"
                                                    placeholder="10">
                                                <div class="invalid-feedback d-none" id="error-quantity"></div>
                                            </div>
                                            <!-- Expire Date -->
                                            <div class="form-group">
                                                <label>Expire Date</label>
                                                <input type="date" class="form-control" name="expire_date">
                                                <div class="invalid-feedback d-none" id="error-expire_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="added_by_admin_id" value="1">
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Batch</button>
                                    <a href="{{ route('batch.manage') }}" class="btn btn-secondary">Back to
                                        Batch</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#batchForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                var isValid = true;

                // Clear all previous validation errors
                $('.invalid-feedback').addClass('d-none').text('');
                $('.form-control').removeClass('is-invalid');

                // Validate required fields
                $('#batchForm .form-control').each(function() {
                    var value = $(this).val();
                    var name = $(this).attr('name');

                    // Check for non-empty values
                    if (!value) {
                        $('#error-' + name).removeClass('d-none').text('This field is required.');
                        $(this).addClass('is-invalid');
                        isValid = false;
                    }

                    // Additional validation for numeric fields
                    if ($(this).attr('type') === 'number' && value) {
                        var numValue = parseFloat(value);
                        if (numValue <= 0) {
                            $('#error-' + name).removeClass('d-none').text(
                                'The value must be greater than zero.');
                            $(this).addClass('is-invalid');
                            isValid = false;
                        }
                    }
                });

                if (!isValid) {
                    return; // Stop the form submission if validation fails
                }

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                );

                var formData = new FormData(this);

                // Perform form submission via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#batchForm')[0].reset(); // Clear form fields on success
                        } else {
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).removeClass('d-none').text(value[
                                        0]);
                                    $('input[name="' + key + '"], select[name="' + key +
                                        '"]').addClass('is-invalid');
                                });
                            } else {
                                toastr.error(response.message || 'Failed to add batch');
                            }
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html('Submit Batch');
                    }
                });
            });
        });
    </script>
@endsection
