@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Add Product</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <form method="POST" action="{{ route('product.submit') }}" enctype="multipart/form-data"
                                id="productForm">

                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Chicken 500g">
                                                <div class="invalid-feedback d-none" id="error-name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Product Code</label>
                                                <input type="text" class="form-control" name="product_code"
                                                    placeholder="PR00012">
                                                <div class="invalid-feedback d-none" id="error-product_code"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Measurement Unit</label>
                                                <select class="form-control" name="measurement_unit">
                                                    <option value="kg">kg</option>
                                                    <option value="pcs">pcs</option>
                                                    <option value="g">g</option>
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-measurement_unit"></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Product Description</label>
                                                <textarea class="form-control" name="description" rows="3" placeholder="Chicken 500g without skinless"></textarea>
                                                <div class="invalid-feedback d-none" id="error-description"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Product Image</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFile"
                                                        name="product_image" accept=".jpg, .jpeg, .png">
                                                    <label class="custom-file-label" for="customFile">Choose Image</label>
                                                    <div class="invalid-feedback" id="error-product_image"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="added_by_admin_id" value="1">
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Product</button>
                                    <a href="{{ route('product.manage') }}" class="btn btn-secondary">Back to
                                        Products</a>
                                </div>
                            </form>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('scripts')
    <script src="{{ asset('js/product-actions.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#productForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                );

                // Clear all previous validation errors
                $('.invalid-feedback').addClass('d-none').text('');
                $('.form-control, .custom-file-input').removeClass(
                    'is-invalid'); // Ensure to include file input

                var formData = new FormData(this);

                // Perform form submission via AJAX
                $.ajax({
                    url: '{{ route('product.submit') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#productForm')[0].reset(); // Clear form fields on success
                            $('.custom-file-label').html(
                                'Choose Image'); // Reset the custom file label
                        } else {
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    if (key ===
                                        'product_image'
                                    ) { // Specific case for file input
                                        $('#error-' + key).removeClass('d-none').text(
                                            value[0]);
                                        $('#customFile').addClass(
                                            'is-invalid'
                                        ); // Add is-invalid class to file input
                                    } else {
                                        $('#error-' + key).removeClass('d-none').text(
                                            value[0]);
                                        $('input[name="' + key + '"], select[name="' +
                                            key + '"]').addClass('is-invalid');
                                    }
                                });
                            } else {
                                toastr.error(response.message || 'Failed to add product');
                            }
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html('Submit Product');
                    }
                });
            });
        });
    </script>
@endsection
