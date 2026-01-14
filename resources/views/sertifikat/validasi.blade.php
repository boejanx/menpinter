<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport" />
    <title>BANGKOMPAS</title>
    <link href="{{ asset('assets/vendor/css/core.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" rel="stylesheet">
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <link href="{{ asset('assets/img/logo/logo_kecil.png') }}" rel="icon" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Verify Email -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body p-0">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a class="app-brand-link gap-2" href="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1">
                                <span class="app-brand-logo demo"><span class="text-primary">
                                        <img alt="Logo" src="{{ asset('assets/img/logo/logo.png') }}" width="256">
                                    </span>
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        
                        <h5 class="text-center mb-4 text-primary">VALIDASI KEASLIAN SERTIFIKAT</h5>

                        <!-- Tampilkan notifikasi jika ada error -->
                        @if(isset($error))
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Tidak Ditemukan',
                                    text: '{{ $error }}',
                                    confirmButtonText: 'OK'
                                });
                            </script>
                        @endif

                        @if (!isset($peserta))
                            <div class="alert alert-danger text-center">
                                <img alt="Logo" src="{{ asset('assets/img/icons/0.png') }}" width="128">
                                <br><br><strong>Data Sertifikat Tidak Ditemukan!</strong> Pastikan anda mengunduh sertifikat melalui aplikasi bangkompas.
                            </div>
                        @endif


                        <!-- Jika data ditemukan, tampilkan detail peserta -->
                        @if(isset($peserta))
                            <div class="row">
                              <table class="table table-striped">
                                <tbody>
                                  <tr>
                                    <td>NIP</td>
                                    <td>{{ $peserta->user->nip }}</td>
                                  </tr>
                                  <tr>
                                    <td>Nama</td>
                                    <td>{{ $peserta->user->name }}</td>
                                  </tr>
                                  <tr>
                                    <td>Kegiatan</td>
                                    <td>{{ $peserta->bangkom->nama_kegiatan }}</td>
                                  </tr>
                                  <tr>
                                    <td>Sertifikat</td>
                                    <td>{{ $peserta->sertifikat }}</td>
                                  </tr> 
                                </tbody>
                              </table>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /Verify Email -->
            </div>
        </div>
    </div>
</body>
</html>
