<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>MENPINTER</title>
    <link href="{{ asset('assets/vendor/css/core.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <link href="{{ asset('assets/img/logo/logo_kecil.png') }}" rel="icon" type="image/x-icon" />
    <link href="https://challenges.cloudflare.com" rel="preconnect">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

</head>

<body>
    <div class="authentication-wrapper authentication-cover">
        <a class="app-brand auth-cover-brand gap-2" href="{{ route('login') }}">
            <span class="app-brand-logo demo">
                <span class="text-primary">

                    <img alt="Logo" src="{{ asset('assets/img/logo/logo.png') }}" width="256">
                </span>
            </span>
        </a>
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-9 col-xl-9 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img alt="Login image" class="img-fluid" src="{{ asset('assets/img/illustrations/login.png') }}" style="opacity: 70%" width="60%" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-3 col-xl-3 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <h5 class="mb-1">
                        ASN PEMKAB PEKALONGAN
                    </h5>
                    <p class="mb-1">Gunakan akun polakesatu untuk masuk.</p>
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4">

                    </div>
                    <!-- /Logo -->
                    <form action="{{ route('login') }}" autocomplete="off" id="formAuthentication" method="POST" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label for="nip">NIP</label>
                            <input autofocus class="form-control" id="nip" name="nip" placeholder="Masukkan NIP Anda" required type="text">
                        </div>

                        <div class="mb-4 form-password-toggle">
                            <label for="password">Password</label>
                            <input class="form-control" id="password" name="password" placeholder="••••••••••••" required type="password">
                        </div>
                        <input id="turnstile-token" name="cf-turnstile-response" type="hidden">
                        <div class="text-center mb-3">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAABgIWBqjs8oqnJRF" data-callback="setTurnstileToken"></div>
                        </div>
                        <button class="btn btn-primary d-grid w-100" type="submit">Login dengan Polakesatu</button>
                        <div class="text-center mt-3" id="login-spinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </form>
                    <div class="divider my-6">
                        <div class="divider-text">atau</div>
                    </div>
                    <h5 class="mb-2">Untuk masyarakat Umum</h5>
                    <a class="btn btn-google-plus w-100" href="{{ route('google.login') }}">
                        <i class="bxl bx-google"></i> Login dengan Google
                    </a>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
</body>

</html>
