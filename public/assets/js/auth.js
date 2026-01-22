$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#formAuthentication').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const url = form.attr('action');
    const data = form.serialize();
    const submitBtn = form.find('button[type="submit"]');

    const nip = $('#nip').val().trim();
    const password = $('#password').val().trim();
    const turnstileToken = $('#turnstile-token').val();

    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');

    if (!nip || !password) {
        if (!nip) {
            $('#nip')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">NIP wajib diisi.</div>');
        }
        if (!password) {
            $('#password')
                .addClass('is-invalid')
                .after('<div class="invalid-feedback">Password wajib diisi.</div>');
        }
        return;
    }

    if (!turnstileToken) {
        Swal.fire({
            icon: 'error',
            title: 'Verifikasi Gagal',
            text: 'Silakan tunggu verifikasi keamanan selesai.'
        });
        return;
    }

    $('#login-spinner').show();
    submitBtn.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        dataType: 'json',
        timeout: 8000,

        success(response) {
            if (response.success) {
                window.location.href = response.redirect_to;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: response.message || 'NIP atau password salah.',
                });
            }
        },

        error(xhr, status) {
            if (status === 'timeout') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Habis',
                    text: 'Koneksi ke server terlalu lama.'
                });
                return;
            }

            if (xhr.status === 422) {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'NIP atau password salah.'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Terjadi kesalahan pada sistem.'
                });
            }
        },

        complete() {
            $('#login-spinner').hide();
            submitBtn.prop('disabled', false).text('Login');
        }
    });
});

function setTurnstileToken(token) {
    $('#turnstile-token').val(token);
}
