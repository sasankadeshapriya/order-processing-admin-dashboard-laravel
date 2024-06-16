<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoicer | Login</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    @include('libraries.style')
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            OTP Verification
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter the OTP sent to your email. We sent to <span
                        id="display-email">{{ $email }}</span></p>

                <form id="otp-form">
                    <input type="hidden" id="email" name="email" value="{{ $email }}">
                    <div class="input-group mb-3">
                        <input type="text" id="otp" name="otp" class="form-control" maxlength="4"
                            placeholder="Enter OTP" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn custom-signin-btn">Verify OTP</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('libraries.script')
    <script>
        $(document).ready(function() {
            $('#otp-form').submit(function(e) {
                e.preventDefault();
                var otpValue = $('#otp').val();
                var emailValue = $('#email').val();

                if (!otpValue || isNaN(otpValue) || otpValue.length !== 4) {
                    toastr.error('Please enter a valid 4-digit OTP.');
                    return; // Stop the function if validation fails
                }

                var positiveOtp = Math.abs(parseInt(otpValue, 10));
                var postData = {
                    email: emailValue,
                    otp: positiveOtp
                };

                // AJAX call to your Laravel proxy
                $.ajax({
                    url: '/api/proxy/verify-otp',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(postData),
                    success: function(response) {
                        if (response.message === "OTP verified successfully!" && response
                            .token) {
                            // Call another AJAX to store the token in Laravel session
                            $.ajax({
                                url: '/store-token',
                                type: 'POST',
                                data: {
                                    token: response.token
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function() {
                                    toastr.success(
                                        'Token stored and OTP Verification Successful'
                                    );
                                    window.location.href = '/';
                                },
                                error: function() {
                                    toastr.error(
                                        'Failed to store token. Please try again.'
                                    );
                                }
                            });
                        } else {
                            toastr.error(response.message || 'Invalid OTP! Please try again.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                        window.location.href = '/login';
                    }
                });
            });
        });
    </script>
</body>

</html>
