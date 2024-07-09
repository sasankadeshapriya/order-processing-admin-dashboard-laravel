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
                                                <th>Client Organization</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments as $key => $payment)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $payment['reference_number'] }}</td>
                                                    <td>{{ $payment['amount'] }}</td>
                                                    <td>
                                                        {{ ucfirst($payment['payment_option']) }}
                                                        @if ($payment['payment_option'] === 'cheque')
                                                            <span class="badge {{ $payment['state'] === 'verified' ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $payment['state'] === 'verified' ? 'Verified' : 'Not Verified' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment['organization_name'] ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment['createdAt'])->format('Y-m-d H:i') }}</td>
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
                                                <th>Payment Option</th>
                                                <th>Client Organization</th>
                                                <th>Created At</th>
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
    <script src="{{ asset('js/allpayment-action.js') }}"></script>
@endsection
