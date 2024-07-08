@extends('layouts.app')

@section('title', 'Payments')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Payments</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Manage Payments</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
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
                                                <th>#</th>
                                                <th>Reference Number</th>
                                                <th>Amount</th>
                                                <th>Payment Option</th>
                                                <th>State</th>
                                                <th>Client Organization</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments as $key => $payment)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $payment['reference_number'] }}</td>
                                                    <td>{{ $payment['amount'] }}</td>
                                                    <td>{{ ucfirst($payment['payment_option']) }}</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input toggle-state" type="checkbox"
                                                                   data-payment-id="{{ $payment['id'] }}"
                                                                   data-checked="{{ $payment['state'] === 'verified' ? 'true' : 'false' }}"
                                                                   {{ $payment['state'] === 'verified' ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                {{ ucfirst($payment['state']) }}
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $payment['organization_name'] ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment['createdAt'])->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm delete-payment" data-id="{{ $payment['id'] }}">
                                                            <i class="fas fa-trash" title="Delete Payment"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Reference Number</th>
                                                <th>Amount</th>
                                                <th>Payment Option</th>
                                                <th>State</th>
                                                <th>Client Organization</th>
                                                <th>Created At</th>
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
    <script>
        $(document).ready(function() {
            // Handle checkbox change
            $('.toggle-state').change(function() {
                const paymentId = $(this).data('payment-id');
                const checked = $(this).prop('checked');
                const newState = checked ? 'verified' : 'not verified';

                // Example API endpoint to update payment state
                $.ajax({
                    url: `/api/payment/${paymentId}/state`,
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({ state: newState }),
                    success: function(response) {
                        console.log('Payment state updated successfully:', response);
                        // Optionally update UI or show success message
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to update payment state:', error);
                        // Handle error or show error message
                    }
                });
            });

            // Example for delete payment button click handler
            $('.delete-payment').click(function() {
                const paymentId = $(this).data('id');
                
                // Example API endpoint to delete payment
                $.ajax({
                    url: `/api/payment/${paymentId}`,
                    method: 'DELETE',
                    success: function(response) {
                        console.log('Payment deleted successfully:', response);
                        // Optionally update UI or show success message
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to delete payment:', error);
                        // Handle error or show error message
                    }
                });
            });
        });
    </script>
@endsection
