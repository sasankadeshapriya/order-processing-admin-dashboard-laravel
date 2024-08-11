@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Invoices</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Invoices</li>
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
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Client</th>
                                                <th>Employee</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Balance</th>
                                                <th>Discount</th>
                                                <th>Credit Period End Date</th>
                                                <th>Created At</th>
                                                <th>Products</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice['reference_number'] }}</td>
                                                    <td>{{ $invoice['client']['organization_name'] ?? 'N/A' }}</td>
                                                    <td>{{ $invoice['employee']['name'] ?? 'N/A' }}</td>
                                                    <td>{{ $invoice['total_amount'] }}</td>
                                                    <td>{{ $invoice['paid_amount'] }}</td>
                                                    <td>{{ $invoice['balance'] }}</td>
                                                    <td>{{ $invoice['discount'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($invoice['credit_period_end_date'])->format('Y-m-d') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($invoice['createdAt'])->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-sm toggle-products" data-id="{{ $invoice['id'] }}">
                                                            <i class="fas fa-eye"></i> Toggle Products
                                                        </button>
                                                        @if (isset($invoice['products']))
                                                            <div class="products-details" id="products-{{ $invoice['id'] }}" style="display: none;">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Product Name</th>
                                                                            <th>SKU</th>
                                                                            <th>Quantity</th>
                                                                            <th>Sum</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($invoice['products'] as $product)
                                                                            <tr>
                                                                                <td>{{ $product['product_name'] ?? 'N/A' }}</td>
                                                                                <td>{{ $product['batch_id'] }}</td>
                                                                                <td>{{ $product['quantity'] }}</td>
                                                                                <td>{{ $product['sum'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm delete-invoice" data-id="{{ $invoice['id'] }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Client</th>
                                                <th>Employee</th>
                                                <th>Total Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Balance</th>
                                                <th>Discount</th>
                                                <th>Credit Period End Date</th>
                                                <th>Created At</th>
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
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js/invoice-action.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Destroy any existing DataTable instance
            if ($.fn.dataTable.isDataTable('#example1')) {
                $('#example1').DataTable().destroy();
            }

            // Initialize DataTable
            $('#example1').DataTable({
                "order": [[0, "asc"]]
            });

            // Toggle products visibility
            $('.toggle-products').click(function() {
                const invoiceId = $(this).data('id');
                const productsDetails = $(`#products-${invoiceId}`);
                if (productsDetails.length) {
                    productsDetails.toggle();
                } else {
                    console.error(`Products details not found for invoice ID ${invoiceId}`);
                }
            });
        });
    </script>
@endsection
