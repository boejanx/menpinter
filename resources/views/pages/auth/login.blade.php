<!DOCTYPE html>
<html  lang="id" class="light-style  customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{base_url}/assets/" data-template="vertical-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>e-Personal</title>
    
    <meta name="description" content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{base_url}/assets/vendor/fonts/fontawesome.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/vendor/css/core.css">
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/css/demo.css">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{base_url}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Page -->
  <link rel="stylesheet" href="{base_url}/assets/vendor/css/pages/page-auth.css">
</head>

<body>
<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
      <div class="w-100 d-flex justify-content-center">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand mb-5">
          <a href="index.html" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
              <!-- logo -->
            </span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-1">SIMPEG INTEGRASI 👋</h4>
        <p class="mb-4">Silahkan masuk untuk melihat data anda.</p>
        {pesan}
        <form id="formAuthentication" class="mb-3" action="{base_url}/auth/login" method="POST" autocomplete="off">
          <div class="mb-3">
            <label for="nip" class="form-label">NIP / Username</label>
            <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP Anda" autofocus autocomplete="off">
          </div>
          <div class="mb-6 form-password-toggle">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password" autocomplete="off">Password</label>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
          </div>
          <div class="mt-4">
            <div class="d-flex justify-content-between">
              <div class="form-check mb-0 ms-2">
                
              </div>
              <a href="auth-forgot-password-cover.html">
                <p class="mb-4">Forgot Password?</p>
              </a>
            </div>
          </div>
          <button class="btn btn-primary d-grid w-100">
            Masuk
          </button>
        </form>
        <div class="divider my-6">
          <div class="divider-text"><img src="{base_url}/assets/img/logo/logo.png" alt="logo" height="32px"></div>
        </div>
        <div class="d-flex justify-content-center">
          <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-facebook me-1_5">
            <i class="bx bxl-facebook-circle"></i>
          </a>

          <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-twitter me-1_5">
            <i class="tf-icons bx bxl-twitter"></i>
          </a>

          <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-github me-1_5">
            <i class="bx bxl-github"></i>
          </a>

          <a href="javascript:;" class="btn btn-sm btn-icon rounded-circle btn-text-google-plus">
            <i class="tf-icons bx bxl-google"></i>
          </a>
        </div>
      </div>
    </div>
    <!-- /Login -->
  </div>
</div>
  <!-- Core JS -->
  <!-- build:js {base_url}/assets/vendor/js/core.js -->
  <script src="{base_url}/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="{base_url}/assets/vendor/libs/popper/popper.js"></script>
  <script src="{base_url}/assets/vendor/js/bootstrap.js"></script>
  <script src="{base_url}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <!-- Vendors JS -->
  <script src="{base_url}/assets/js/main.js"></script>
  
</body>
</html>

