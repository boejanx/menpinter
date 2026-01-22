<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>MENPINTER</title>
    <link href="{{ asset('assets/vendor/css/core.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/img/logo/logo_kecil.png') }}" rel="icon" type="image/x-icon" />
</head>

<body>
    <main class="authentication-wrapper authentication-cover">
        
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-9 col-xl-9 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img alt="Login image" class="img-fluid" src="{{ asset('assets/img/illustrations/login.webp') }}" style="opacity: 70%" width="60%" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-3 col-xl-3 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-100 w-sm-400 mx-auto">
                    <div class="app-brand justify-content-center mb-4">
                        <img alt="Logo" src="{{ asset('assets/img/logo/logo.png') }}" width="256">
                    </div>
                    <div class="card">
                        <div class="card-body">

                    <!-- Logo -->
                    
                    <div class="alert alert-info"><i class="fas fa-info">Gunakan Akun Polakesatu Untuk Masuk</i></div>
                    <!-- /Logo -->
                    <form action="{{ route('login') }}" autocomplete="off" id="formAuthentication" method="POST" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label for="nip">NIP</label>
                            <input autofocus class="form-control form-control-lg" id="nip" name="nip" placeholder="Masukkan NIP Anda" required type="text">
                        </div>

                        <div class="mb-4 form-password-toggle">
                            <label for="password">Password</label>
                            <input class="form-control form-control-lg" id="password" name="password" placeholder="••••••••••••" required type="password">
                        </div>
                        <input id="turnstile-token" name="cf-turnstile-response" type="hidden">
                        <div class="text-center mb-3">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAABgIWBqjs8oqnJRF" data-callback="setTurnstileToken"></div>
                        </div>
                        <button class="btn btn-primary btn-lg d-flex w-100" type="submit">
                            <i class='bx bx-key-alt me-2'></i> Login Polakesatu
                        </button>
                        <div class="text-center mt-3" id="login-spinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </form>
                    </div>
                    </div>
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
    </main>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</body>

</html>
