<!DOCTYPE html>
<html lang="id"  class=" layout-menu-fixed layout-compact " data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="title" content="MENPINTER">

<meta name="description" content="MENPINTER merupakan aplikasi pengembangan kompetensi ASN berbasis web yang mendukung perencanaan, pemantauan, dan evaluasi peningkatan kompetensi secara terstruktur, transparan, dan akuntabel.">

<meta name="robots" content="index, follow">

<link rel="canonical" href="{{ url()->current() }}">

<meta name="theme-color" content="#696cff">

<meta name="referrer" content="strict-origin-when-cross-origin">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="MENPINTER">
<meta property="og:description" content="Aplikasi pengembangan kompetensi ASN berbasis web yang terintegrasi, transparan, dan akuntabel.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('assets/img/og-image.png') }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="MENPINTER">
<meta name="twitter:description" content="Aplikasi pengembangan kompetensi ASN berbasis web.">
<meta name="twitter:image" content="{{ asset('assets/img/og-image.png') }}">

<title>MENPINTER</title>

<link rel="icon" type="image/png" href="{{ asset('assets/img/logo/logo_kecil.png') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @include('partials.sidebar')
            <!-- /Sidebar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('partials.navbar')
                <!-- /Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <div id="app" data-page="{{ Route::currentRouteName() ?? 'dashboard' }}">
                            {{ $slot }}
                        </div>
                    </div>
                    <!-- /Content -->

                    <!-- Footer -->
                    @include('partials.footer')
                    <!-- /Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- /Content wrapper -->
            </div>
            <!-- /Layout container -->
        </div>
        <!-- /Layout container -->
    </div>
    <div id="layout-menu-backdrop" class="layout-menu-backdrop"></div>
</body>

</html>