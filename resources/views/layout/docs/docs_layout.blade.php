<!DOCTYPE html>
<html lang="en">

<head>

    @include('layout.docs.docs_header')

</head>

<body>

<div class="layout-wrapper layout-content-navbar">

    <div class="layout-container">

        {{-- Sidebar --}}
        @include('layout.docs.docs_sidebar')

        {{-- Main --}}
        <div class="layout-page">

            <div class="container-fluid">

                <div class="docs-content">

                    @yield('content')

                </div>

            </div>

            @include('layout.docs.docs_footer')

        </div>

    </div>

</div>

@yield('page-script')

</body>

</html>