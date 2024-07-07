@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Employee</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Add Employee</li>
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
                            <form method="POST" action="{{ route('employee.submit') }}" enctype="multipart/form-data" id="employeeForm">
                                @csrf
                                <div class="card-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" placeholder="Enter email address" >
                <div class="invalid-feedback d-none" id="error-email"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" >
                <div class="invalid-feedback d-none" id="error-password"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter full name">
                <div class="invalid-feedback d-none" id="error-name"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>NIC</label>
                <input type="text" class="form-control" name="nic" placeholder="Enter NIC number (e.g. 198201409894 or 810509871V)">
                <div class="invalid-feedback d-none" id="error-nic"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phone_no" placeholder="Enter phone number">
                <div class="invalid-feedback d-none" id="error-phone_no"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Commission Rate<small> (%)</small></label>
                <input type="number" class="form-control" name="commission_rate" placeholder="Enter commission rate" step="0.1">
                <div class="invalid-feedback d-none" id="error-commission_rate"></div>
            </div>
        </div>
        <input type="hidden" name="added_by_admin_id" value=1>
    </div>
</div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Employee</button>
                                    <a href="{{ route('employee.manage') }}" class="btn btn-secondary">Back to Employees</a>
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
    <script src="{{ asset('js/employee-actions.js') }}"></script>
    <script>
$(document).ready(function() {
    // Function to validate NIC
    function validateNIC(nic) {
        const newNICPattern = /^\d{12}$/;
        const oldNICPattern = /^\d{9}[vVxX]$/;
        return newNICPattern.test(nic) || oldNICPattern.test(nic);
    }

    // Event listener to capitalize letters in NIC field
    $('input[name="nic"]').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    $('#employeeForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        $('#submitBtn').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
        );

        // Clear all previous validation errors
        $('.invalid-feedback').addClass('d-none').text('');
        $('.form-control').removeClass('is-invalid');

        // Perform custom required validation
        let hasError = false;
        $('#employeeForm input').each(function() {
            if ($(this).val() === '' && $(this).attr('name') !== 'name' && $(this).attr('name') !== 'nic' && $(this).attr('name') !== 'phone_no' && $(this).attr('name') !== 'commission_rate') {
                hasError = true;
                $(this).addClass('is-invalid');
                $('#error-' + $(this).attr('name')).removeClass('d-none').text('This field is required');
            }
        });

        // Validate NIC
        const nic = $('input[name="nic"]').val();
        if (nic !== '' && !validateNIC(nic)) {
            hasError = true;
            $('input[name="nic"]').addClass('is-invalid');
            $('#error-nic').removeClass('d-none').text('Invalid NIC format');
        }

        if (hasError) {
            $('#submitBtn').prop('disabled', false).html('Submit Employee');
            return;
        }

        // Prepare form data
        const formDataArray = $(this).serializeArray();
        const formData = {};

        // Convert relevant fields to correct types
        formDataArray.forEach(field => {
            if (field.name === 'commission_rate') {
                formData[field.name] = parseFloat(field.value) || 0;
            } else if (field.name === 'added_by_admin_id') {
                formData[field.name] = parseInt(field.value, 10);
            } else {
                formData[field.name] = field.value;
            }
        });

        console.log("Formatted Data: ", formData); // Log formatted data

        // Perform form submission via AJAX
        $.ajax({
            url: '{{ route('employee.submit') }}',
            type: 'POST',
            contentType: 'application/json', // Explicitly set the content type
            data: JSON.stringify(formData), // Ensure data is sent as a JSON string
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#employeeForm')[0].reset(); // Clear form fields on success
                } else {
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#error-' + key).removeClass('d-none').text(value[0]);
                            $('input[name="' + key + '"]').addClass('is-invalid');
                        });
                    } else {
                        toastr.error(response.message || 'Failed to add employee');
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('Submit Employee');
            }
        });
    });
});
</script>


@endsection
