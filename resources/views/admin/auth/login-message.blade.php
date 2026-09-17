<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('admin/assets').'/' }}" data-template="vertical-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>QAMR HR - Login Message</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/pages/page-auth.css') }}" />

    <style>
        /* Center animation and message */
        .pending-animation {
            width: 120px;
            height: 120px;
            margin: 0 auto 25px auto;
            display: block;
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
            100% { transform: rotate(360deg); }
        }

        .message-box {
            text-align: center;
        }

        .message-box h3 {
            font-size: 1.75rem;
            margin-bottom: 15px;
        }

        .message-box p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">

        <!-- Left Illustration -->
        <div class="d-none d-lg-flex col-lg-7 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="{{ asset('admin/assets/img/illustrations/auth-login-illustration-light.png') }}" alt="auth-login-cover" class="img-fluid my-5 auth-illustration" data-app-light-img="illustrations/auth-login-illustration-light.png" data-app-dark-img="illustrations/auth-login-illustration-dark.png"/>
                <img src="{{ asset('admin/assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png"/>
            </div>
        </div>

        <!-- Message Panel -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto message-box">
                <!-- Animated Pending Icon -->
                <svg class="pending-animation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 64 64">
                    <circle cx="32" cy="32" r="30" stroke="#7367F0" stroke-width="4" opacity="0.25"/>
                    <path fill="#7367F0" d="M32 2a30 30 0 1030 30A30 30 0 0032 2zm0 4a26 26 0 1126 26A26 26 0 0132 6z"/>
                    <path fill="#7367F0" d="M32 12v20l14 8" stroke="#7367F0" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <h3 class="fw-bold">Access Pending</h3>
                <p>
                    {{ $message ?? 'Your device login is being verified by admin. Please wait until approval.' }}
                </p>

                <a href="{{ route('admin.login') }}" class="btn btn-primary w-100">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>
</body>
</html>


<script>
$(document).ready(function() {
    const deviceId = "{{ $device_id }}"; // from Blade view

    if (!deviceId) return;

    // Function to check approval status
    function checkApprovalStatus() {
        $.ajax({
            url: "{{ route('admin.check-device-status') }}",
            type: "GET",
            data: { device_id: deviceId },
            success: function(response) {
                if (response.status === true) {
                    console.log("Device approved! Attempting auto-login...");

                    // Attempt auto-login
                    $.ajax({
                        url: "{{ route('admin.auto-login') }}",
                        type: "GET",
                        data: { device_id: deviceId },
                        success: function(loginResponse) {
                            if (loginResponse.status === true) {
                                console.log("Auto-login successful, redirecting...");
                                window.location.href = loginResponse.redirect_url;
                            } else {
                                console.warn("Auto-login failed:", loginResponse.message);
                            }
                        },
                        error: function() {
                            console.error("Error during auto-login request");
                        }
                    });

                } else {
                    console.log("Device not yet approved...");
                }
            },
            error: function() {
                console.error("Error checking device approval status");
            }
        });
    }

    // Check every 5 seconds (5000 ms)
    setInterval(checkApprovalStatus, 5000);
});
</script>

