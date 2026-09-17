<footer class="content-footer footer bg-footer-theme border-top">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-3">

        <div>
            © {{ date('Y') }}
            <strong>QAMAR HIRE</strong>
            Documentation
        </div>

        <div>

            <span class="text-muted me-3">
                Version 1.0
            </span>

            <a href="{{ route('docs.index') }}" class="text-decoration-none">
                Documentation Home
            </a>

        </div>

    </div>
</footer>

<!-- Core JS -->

<script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>

<script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>

<script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>

<script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

<script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>

<script>
    // Prevent the theme's menu init from auto-scrolling the docs sidebar
    // to the active link on every page load / link click.
    if (window.Helpers) {
        window.Helpers.scrollToActive = function () {};
    }
</script>

<script src="{{ asset('admin/assets/js/main.js') }}"></script>

<script>

$(function(){

    // Highlight current menu automatically

    $('.docs-sidebar .nav-link').each(function(){

        if($(this).attr('href') == window.location.href){

            $(this).addClass('active');

        }

    });

});

</script>