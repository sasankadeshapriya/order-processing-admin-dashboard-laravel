@extends('layouts.app')

@section('title', 'Cheque Payments')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Cheque Payments</h1>
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

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="paymentsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Reference Number</th>
                                                <th>Amount</th>
                                                <th>Client Organization</th>
                                                <th>Created At</th>
                                                <th>State</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments as $key => $payment)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $payment['reference_number'] }}</td>
                                                    <td>{{ $payment['amount'] }}</td>
                                                    <td>{{ $payment['organization_name'] ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment['createdAt'])->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input state-toggle" type="checkbox" id="stateToggle_{{ $payment['id'] }}" data-payment-id="{{ $payment['id'] }}" {{ $payment['state'] == 'verified' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="stateToggle_{{ $payment['id'] }}">
                                                                <span class="state-text">{{ ucfirst($payment['state']) }}</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm delete-payment" data-id="{{ $payment['id'] }}">
                                                            <i class="fas fa-trash"></i>
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
                                                <th>Client Organization</th>
                                                <th>Created At</th>
                                                <th>State</th>
                                                <th>Actions</th>
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
    <script src="{{ asset('js/payment-action.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#paymentsTable').DataTable();

            // Handle state toggle checkbox change
            $('.state-toggle').on('change', function() {
                var checkbox = $(this); // Store reference to checkbox
                var paymentId = checkbox.data('payment-id');
                var isChecked = checkbox.prop('checked');
                var state = isChecked ? 'verified' : 'not-verified';
                var stateTextElement = checkbox.closest('tr').find('.state-text');

                // Send state update to server
                $.ajax({
                    url: '/payment/toggle-state/' + paymentId,
                    method: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        state: state
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('State updated successfully');
                            // Update state text
                            stateTextElement.text(state.charAt(0).toUpperCase() + state.slice(1).replace('-', ' '));
                        } else {
                            toastr.error('Failed to update state');
                            // Revert checkbox state if update failed
                            checkbox.prop('checked', !isChecked);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Server error: Unable to update state', error);
                        toastr.error('Server error: Unable to update state');
                        // Revert checkbox state if error occurred
                        checkbox.prop('checked', !isChecked);
                    }
                });
            });
        });
    </script>
@endsection
