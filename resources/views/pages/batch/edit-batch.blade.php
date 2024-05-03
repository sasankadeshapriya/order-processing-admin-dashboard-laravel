@extends('layouts.app')

@section('title', 'Edit Batch')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Batch</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Edit Batch</li>
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
                            <form method="POST" action="{{ route('batch.update', $batch->id) }}" id="editBatchForm"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <p>All prices in <code>.LKR</code> format.</p>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>SKU</label>
                                                <input type="text" class="form-control" name="sku"
                                                    value="{{ $batch->sku }}">
                                                <div class="invalid-feedback d-none" id="error-sku"></div>
                                            </div>
                                            @if (!empty($products))
                                                <div class="form-group">
                                                    <label>Product</label>
                                                    <select class="form-control" name="product_id">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product['id'] }}"
                                                                @if ($batch->product_id == $product['id']) selected @endif>
                                                                {{ $product['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback" id="error-product_id"></div>
                                                </div>
                                            @else
                                                <p>No products available</p>
                                            @endif

                                            <div class="form-group">
                                                <label>Buy Price</label>
                                                <input type="number" class="form-control" name="buy_price" step="0.01"
                                                    value="{{ $batch->buy_price }}">
                                                <div class="invalid-feedback d-none" id="error-buy_price"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Cash Price</label>
                                                <input type="number" class="form-control" name="cash_price" step="0.01"
                                                    value="{{ $batch->cash_price }}">
                                                <div class="invalid-feedback d-none" id="error-cash_price"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Cheque Price</label>
                                                <input type="number" class="form-control" name="check_price" step="0.01"
                                                    value="{{ $batch->check_price }}">
                                                <div class="invalid-feedback d-none" id="error-check_price"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Credit Price</label>
                                                <input type="number" class="form-control" name="credit_price"
                                                    step="0.01" value="{{ $batch->credit_price }}">
                                                <div class="invalid-feedback d-none" id="error-credit_price"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Quantity</label>
                                                <input type="number" class="form-control" name="quantity" step="0.05"
                                                    value="{{ $batch->quantity }}">
                                                <div class="invalid-feedback d-none" id="error-quantity"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Expire Date</label>
                                                <input type="date" class="form-control" name="expire_date"
                                                    value="{{ $batch->expire_date }}">
                                                <div class="invalid-feedback d-none" id="error-expire_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update Batch</button>
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
    <script src="https://cdn.jsdelivr.net/npm/toastr"></script>
    <script>
        $(document).ready(function() {
            // Store initial form data
            var initialFormData = $('#editBatchForm').serialize();

            // Store initial file input value
            var initialFile = $('#customFile').val();

            $('#editBatchForm').submit(function(event) {
                event.preventDefault();

                // Get current form data
                var currentFormData = $(this).serialize();

                // Get current file input value
                var currentFile = $('#customFile').val();

                // Check if either form data or file input has changed
                if (initialFormData !== currentFormData || initialFile !== currentFile) {
                    var formData = new FormData(this);
                    $('#editBatchForm button[type="submit"]').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                    );

                    $('.invalid-feedback').addClass('d-none').text('');
                    $('.form-control, .custom-file-input').removeClass('is-invalid');

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                initialFormData =
                                currentFormData; // Update the stored form data
                                initialFile = currentFile; // Update the stored file input value
                                // Optionally update the UI or perform any other actions after successful update
                            } else {
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#error-' + key).removeClass('d-none').text(
                                            value[0]);
                                        $('input[name="' + key + '"], select[name="' +
                                            key + '"]').addClass('is-invalid');
                                    });
                                } else {
                                    toastr.error(response.message || 'Failed to update batch');
                                }
                            }
                        },
                        error: function(xhr) {
                            // Display API error messages in toastr
                            toastr.error('Error: ' + (xhr.responseJSON.message || xhr
                                .statusText));
                        },
                        complete: function() {
                            $('#editBatchForm button[type="submit"]').prop('disabled', false)
                                .html('Update Batch');
                        }
                    });
                } else {
                    toastr.info('No changes detected. Please modify the data before updating.');
                }
            });

            bsCustomFileInput.init(); // Ensure custom file input is properly initialized
        });
    </script>
@endsection
