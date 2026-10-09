{{-- Masked "Enter the OTP sent to your WhatsApp number ending in 3401."
     description shared by every Partner and Customer OTP step
     (App\Support\OtpDestination + otp-destination.js). Loaded once per page,
     whichever OTP partials the page includes. --}}
@once
<script src="{{ asset('worker/js/otp-destination.js') }}?v={{ @filemtime(public_path('worker/js/otp-destination.js')) ?: time() }}"
    data-texts="{{ json_encode(\App\Support\OtpDestination::texts()) }}"></script>
@endonce
