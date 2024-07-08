@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Employee</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Edit Employee</li>
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
                            <form method="POST" action="{{ route('employee.update', $employee->id) }}" id="editEmployeeForm">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ $employee->email }}">
                                                <div class="invalid-feedback d-none" id="error-email"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Password 
                                                    <i class="fas fa-info-circle" data-toggle="tooltip" title="Leave blank to keep the current password or Enter a new password to update it."></i>
                                                </label>
                                                <input type="password" class="form-control" name="password" placeholder="Enter new password">
                                                <div class="invalid-feedback d-none" id="error-password"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Commission Rate (%)</label>
                                                <input type="number" class="form-control" name="commission_rate" value="{{ $employee->commission_rate }}" step="0.1">
                                                <div class="invalid-feedback d-none" id="error-commission_rate"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">Update Employee</button>
                                    <a href="{{ route('employee.manage') }}" class="btn btn-secondary">Back to Employees</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Confirm Password Update Modal -->
    <div class="modal fade" id="confirmPasswordUpdateModal" tabindex="-1" role="dialog" aria-labelledby="confirmPasswordUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPasswordUpdateModalLabel">Confirm Password Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the password? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelPasswordUpdateBtn">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPasswordUpdateBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        var initialFormData = $('#editEmployeeForm').serialize();
        var formSubmitting = false;
        var modalConfirmed = false;

        $('#editEmployeeForm').submit(function(event) {
            event.preventDefault();
            var currentFormData = $(this).serialize();
            console.log('Initial Form Data:', initialFormData);
            console.log('Current Form Data:', currentFormData);

            if (initialFormData !== currentFormData && !formSubmitting) {
                var formDataArray = $(this).serializeArray();
                var formData = {};

                formDataArray.forEach(field => {
                    if (field.name === 'commission_rate') {
                        formData[field.name] = parseFloat(field.value) || null;
                    } else {
                        formData[field.name] = field.value;
                    }
                });

                var password = formData['password'];
                if (password && password.trim() !== '') {
                    $('#confirmPasswordUpdateModal').modal('show');
                } else {
                    submitForm(formData);
                }
            } else {
                toastr.info('No changes detected. Please modify the data before updating.');
            }
        });

        $('#confirmPasswordUpdateBtn').on('click', function() {
            modalConfirmed = true;
            $('#confirmPasswordUpdateModal').modal('hide');
            var formDataArray = $('#editEmployeeForm').serializeArray();
            var formData = {};

            formDataArray.forEach(field => {
                if (field.name === 'commission_rate') {
                    formData[field.name] = parseFloat(field.value) || null;
                } else {
                    formData[field.name] = field.value;
                }
            });

            submitForm(formData);
        });

        $('#cancelPasswordUpdateBtn').on('click', function() {
            modalConfirmed = false;
            formSubmitting = false;
        });

        function submitForm(formData) {
    if (!modalConfirmed && formData.password && formData.password.trim() !== '') {
        return;
    }
    formSubmitting = true;
    $('#submitBtn').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
    );

    $('.invalid-feedback').addClass('d-none').text('');

    $.ajax({
        url: $('#editEmployeeForm').attr('action'),
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        processData: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                initialFormData = $('#editEmployeeForm').serialize();
                $('input[name="password"]').val(''); // Clear password field on successful submission
            } else {
                handleErrors(response);
            }
        },
        error: function(xhr) {
            handleErrors(xhr.responseJSON, xhr.status);
        },
        complete: function() {
            formSubmitting = false;
            $('#submitBtn').prop('disabled', false).html('Update Employee');
            modalConfirmed = false;
        }
    });
}


        function handleErrors(response, status) {
            if (status === 409) {
                toastr.error('Email already exists!');
            } else if (status === 400) {
                toastr.error('Validation failed');
                if (response && response.errors) {
                    $.each(response.errors, function(key, value) {
                        $('#error-' + key).removeClass('d-none').text(value[0]);
                        $('input[name="' + key + '"]').addClass('is-invalid');
                    });
                }
            } else if (status === 404) {
                toastr.error('Employee not found');
            } else if (status === 500) {
                toastr.error('Something went wrong!');
            } else {
                toastr.error('Failed to update employee');
            }
        }
    });
</script>
@endsection
