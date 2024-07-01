@extends('layouts.app')

@section('title', 'Batch')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Batch</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Manage Batch</li>
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
                                <a href="{{ route('batch.add') }}" class="btn btn-primary">
                                    Add Batch <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <p>All prices in <code>.LKR</code> format.</p>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Product Code</th>
                                                <th>SKU</th>
                                                <th>Qty</th>
                                                <th>MRP</th>
                                                <th>Cash Price</th>
                                                <th>Cheque Price</th>
                                                <th>Credit Price</th>
                                                <th>Expire Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $key => $item)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $item['Product']['name'] }}</td>
                                                    <td>{{ $item['Product']['product_code'] }}</td>
                                                    <td>{{ $item['sku'] }}</td>
                                                    <td>{{ $item['quantity'] }}</td>
                                                    <td>{{ $item['buy_price'] }}</td>
                                                    <td>{{ $item['cash_price'] }}</td>
                                                    <td>{{ $item['check_price'] }}</td>
                                                    <td>{{ $item['credit_price'] }}</td>
                                                    <td>{{ $item['expire_date'] }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('batch.edit', $item['id']) }}"
                                                                class="btn btn-secondary btn-sm mr-2">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-batch"
                                                                data-id="{{ $item['id'] }}">
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
                                                <th>SKU</th>
                                                <th>Qty</th>
                                                <th>MRP</th>
                                                <th>Cash Price</th>
                                                <th>Cheque Price</th>
                                                <th>Credit Price</th>
                                                <th>Expire Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
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
    <script src="{{ asset('js/batch-action.js') }}"></script>
@endsection

@endsection
