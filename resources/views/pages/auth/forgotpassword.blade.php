<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoicer | Forgot-Password</title>

    @include('libraries.style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <style>
        .spinner-border {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            Forgot-Password
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

                <form id="forgot-password-form">
                    <div class="input-group mb-3" id="email-field">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3" id="otp-field" style="display: none;">
                        <input type="text" id="otp" name="otp" class="form-control" maxlength="4"
                            placeholder="Enter OTP">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
                            </div>
                        </div>
                    </div>
                    <div id="password-fields" style="display: none;">
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="new-password" name="new-password"
                                placeholder="New Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="confirm-password" name="confirm-password"
                                placeholder="Confirm Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn custom-signin-btn btn-block" id="submit-button">
                                <span id="button-text">Request new password</span>
                                <span class="spinner-border spinner-border-sm" id="button-spinner"
                                    style="display: none;"></span>
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mt-3 mb-1">
                    <a href="/login" class="custom-text-color">Login</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('libraries.script')
    <script>
        $(document).ready(function() {
            $('#forgot-password-form').on('submit', function(e) {
                e.preventDefault();
                let email = $('#email').val();
                let otp = $('#otp').val();
                let newPassword = $('#new-password').val();
                let confirmPassword = $('#confirm-password').val();

                // Show loader and disable button
                $('#button-text').hide();
                $('#button-spinner').show();
                $('#submit-button').prop('disabled', true);

                if ($('#email-field').is(':visible')) {
                    $.ajax({
                        url: '/api/proxy/forgot-password',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            email: email
                        }),
                        success: function(response) {
                            if (response.message === 'User not found!') {
                                toastr.error(response.message);
                                $('#email').val('');
                            } else if (response.message === 'OTP sent successfully!') {
                                toastr.success(response.message);
                                $('#email-field').hide();
                                $('#email').prop('required', false);
                                $('#otp-field').show();
                                $('#otp').prop('required', true);
                                $('#submit-button').text('Verify OTP');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', status, error); // Debugging log
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                                $('#email').val('');
                            } else {
                                toastr.error('Error: ' + status + ' ' + error);
                                $('#email').val('');
                            }
                        },
                        complete: function() {
                            $('#button-text').show();
                            $('#button-spinner').hide();
                            $('#submit-button').prop('disabled', false);
                        }
                    });
                } else if ($('#otp-field').is(':visible')) {
                    var otpValue = $('#otp').val();

                    if (!otpValue || isNaN(otpValue) || otpValue.length !== 4) {
                        $('#button-text').show();
                        $('#button-spinner').hide();
                        $('#submit-button').prop('disabled', false);
                        toastr.error('Please enter a valid 4-digit OTP.');
                        $('#otp').val('');
                    } else {
                        var positiveOtp = Math.abs(parseInt(otpValue, 10));

                        $.ajax({
                            url: '/api/proxy/verify-otp',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                email: email,
                                otp: positiveOtp
                            }),
                            success: function(response) {
                                if (response.message === 'Invalid OTP!') {
                                    toastr.error(response.message);
                                    window.location.href =
                                        '/login'; // Redirect to login page on invalid OTP
                                } else if (response.message === 'OTP verified successfully!') {
                                    toastr.success(response.message);
                                    $('#otp-field').hide();
                                    $('#otp').prop('required', false);
                                    $('#password-fields').show();
                                    $('#new-password').prop('required', true);
                                    $('#confirm-password').prop('required', true);
                                    $('#submit-button').text('Change Password');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', status, error); // Debugging log
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    toastr.error(xhr.responseJSON.message);
                                    window.location.href =
                                        '/login'; // Redirect to login page on error
                                } else {
                                    toastr.error('Error: ' + status + ' ' + error);
                                    window.location.href =
                                        '/login'; // Redirect to login page on error
                                }
                            },
                            complete: function() {
                                $('#button-text').show();
                                $('#button-spinner').hide();
                                $('#submit-button').prop('disabled', false);
                            }
                        });
                    }

                } else if ($('#password-fields').is(':visible')) {
                    if (newPassword.length < 8) {
                        toastr.error('The password must be at least 8 characters long.');
                        $('#button-text').show();
                        $('#button-spinner').hide();
                        $('#submit-button').prop('disabled', false);
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        toastr.error('Passwords do not match!');
                        $('#button-text').show();
                        $('#button-spinner').hide();
                        $('#submit-button').prop('disabled', false);
                        return;
                    }

                    $.ajax({
                        url: '/api/proxy/change-password',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            email: email,
                            newPassword: newPassword
                        }),
                        success: function(response) {
                            if (response.message === 'Password changed successfully!') {
                                toastr.success(response.message);
                                window.location.href = '/login';
                            } else {
                                toastr.error('Password change failed!');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', status, error); // Debugging log
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            } else {
                                toastr.error('Error: ' + status + ' ' + error);
                            }
                        },
                        complete: function() {
                            $('#button-text').show();
                            $('#button-spinner').hide();
                            $('#submit-button').prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>
