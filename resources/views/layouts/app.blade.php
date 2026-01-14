<!DOCTYPE html>
<html lang="id"  class=" layout-menu-fixed layout-compact " data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bangkompas') }}</title>
    <!-- Include CSS files -->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=YOUR_PROPERTY_ID&product=inline-share-buttons"></script>
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