@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Edit Product</li>
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
                            <form method="POST" action="{{ route('product.update', $product->id) }}"
                                enctype="multipart/form-data" id="editProductForm">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $product->name }}">
                                                <div class="invalid-feedback d-none" id="error-name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Measurement Unit</label>
                                                <select class="form-control" name="measurement_unit">
                                                    <option value="kg"
                                                        {{ $product->measurement_unit == 'kg' ? 'selected' : '' }}>kg
                                                    </option>
                                                    <option value="pcs"
                                                        {{ $product->measurement_unit == 'pcs' ? 'selected' : '' }}>pcs
                                                    </option>
                                                    <option value="g"
                                                        {{ $product->measurement_unit == 'g' ? 'selected' : '' }}>g</option>
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-measurement_unit"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Product Description</label>
                                                <textarea class="form-control" name="description">{{ $product->description }}</textarea>
                                                <div class="invalid-feedback d-none" id="error-description"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>New Product Image (optional .jpg .png .jpeg)</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFile"
                                                        name="product_image">
                                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                                </div>
                                                <div class="invalid-feedback d-none" id="error-product_image"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Current Product Image</label>
                                            <div class="custom-file">
                                                <img src="{{ $product->product_image }}" alt="Product Image"
                                                    style="width: 100px; height: auto; margin-top: 10px; margin-bottom: 50px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">Update Product</button>
                                    <a href="{{ route('product.manage') }}" class="btn btn-secondary">Back to
                                        Products</a>
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
            var initialFormData = $('#editProductForm').serialize();
            var initialFile = $('#customFile').val(); // Store initial file input value

            $('#editProductForm').submit(function(event) {
                event.preventDefault();
                var currentFormData = $(this).serialize();
                var currentFile = $('#customFile').val(); // Get current file input value

                // Check if either form data or file input has changed
                if (initialFormData !== currentFormData || initialFile !== currentFile) {
                    var formData = new FormData(this);
                    $('#submitBtn').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                    );

                    $('.invalid-feedback').addClass('d-none').text('');
                    $('.form-control, .custom-file-input').removeClass('is-invalid');

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST', // Use POST with _method set to PUT
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                initialFormData =
                                    currentFormData; // Update the stored form data
                                initialFile = currentFile; // Update the stored file input value
                                // Only change the image if a new image file was actually uploaded
                                if (formData.has('product_image') && formData.get(
                                        'product_image').size > 0) {
                                    // Update the image preview to the newly uploaded image
                                    $('img[alt="Product Image"]').attr('src', URL
                                        .createObjectURL(formData.get('product_image')));
                                } else {
                                    var imageUrl = "{{ $product->product_image }}?t=" +
                                        new Date().getTime();
                                    $('img[alt="Product Image"]').attr('src', imageUrl);
                                }
                            } else {
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        $('#error-' + key).removeClass('d-none').text(
                                            value[0]);
                                        $('input[name="' + key + '"], select[name="' +
                                            key + '"]').addClass('is-invalid');
                                    });
                                } else {
                                    toastr.error(response.message ||
                                        'Failed to update product');
                                }
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error: ' + (xhr.responseJSON.message || xhr
                                .statusText));
                        },
                        complete: function() {
                            $('#submitBtn').prop('disabled', false).html('Update Product');
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
