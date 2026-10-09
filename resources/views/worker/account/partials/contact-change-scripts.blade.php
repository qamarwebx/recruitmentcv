{{-- Scripts for the shared contact-change modal: jQuery + Select2 for the
country selector (country-code-select.js), then customer-account.js. --}}
<script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('worker/js/country-code-select.js') }}?v={{ @filemtime(public_path('worker/js/country-code-select.js')) ?: time() }}"></script>
@include('worker.partials.otp-destination-script')
<script src="{{ asset('worker/js/customer-account.js') }}?v={{ @filemtime(public_path('worker/js/customer-account.js')) ?: time() }}"></script>
