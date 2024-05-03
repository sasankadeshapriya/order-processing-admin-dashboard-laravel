@extends('layouts.app')

@section('title', 'Product')

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
                            <li class="breadcrumb-item active">Manage Product</li>
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
                            <div class="card-header">
                                <a href="{{ route('product.add') }}" class="btn btn-primary">
                                    Add Product <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Product Code</th>
                                                <th>Description</th>
                                                <th>Unit</th>
                                                <th>Product Image</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($products as $key => $product)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $product['name'] }}</td>
                                                    <td>{{ $product['product_code'] }}</td>
                                                    <td>{{ $product['description'] }}</td>
                                                    <td>{{ $product['measurement_unit'] }}</td>
                                                    <td>
                                                        <a href="javascript:void(0);"
                                                            onclick="openImagePopup('{{ $product['product_image'] }}')">
                                                            <img src="{{ $product['product_image'] }}" alt="product image"
                                                                width="30px" height="30px">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('product.edit', $product['id']) }}"
                                                                class="btn btn-secondary btn-sm mr-2">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-product"
                                                                data-id="{{ $product['id'] }}">
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
                                                <th>Product Code</th>
                                                <th>Description</th>
                                                <th>Unit</th>
                                                <th>Product Image</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                </div>
                                </table>
                            </div>
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
@section('scripts')
    <script src="{{ asset('js/product-actions.js') }}"></script>
@endsection

@endsection
