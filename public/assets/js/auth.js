$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#formAuthentication').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const url = form.attr('action');
    const data = form.serialize(); // Serialize data dari form
    const submitBtn = form.find('button[type="submit"]');

    // Ambil nilai token CSRF dari meta tag
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Gabungkan token CSRF ke data form    // Ambil nilai input
    const nip = $('#nip').val().trim();
    const password = $('#password').val().trim();

    // Reset error sebelumnya
    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');

    // Validasi manual
    if (nip === '' || password === '') {
        if (nip === '') {
            $('#nip').addClass('is-invalid').after('<div class="invalid-feedback">NIP wajib diisi.</div>');
        }
        if (password === '') {
            $('#password').addClass('is-invalid').after('<div class="invalid-feedback">Password wajib diisi.</div>');
        }

        return; // Hentikan proses
    }

    // Tampilkan spinner & disable tombol
    $('#login-spinner').show();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
        url: url,
        method: 'POST',
        data: dataWithCsrf, // Kirim data dengan token CSRF
        dataType: 'json',
        timeout: 8000, // Batas waktu 8 detik
        success: function (response) {
            if (response.success) {
                window.location.href = response.redirect_to;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: response.message || 'NIP atau password salah.',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function (xhr, status) {
            if (status === 'timeout') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Habis',
                    text: 'Koneksi ke server terlalu lama. Silakan coba lagi.',
                });
                return;
            }

            if (xhr.status === 422) {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: 'NIP atau password salah. Gunakan NIP dan password yang digunakan untuk login ke Polakesatu.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                });
            }
        },

        complete: function () {
            // Sembunyikan spinner & enable tombol
            $('#login-spinner').hide();
            submitBtn.prop('disabled', false).text('Login');
        }
    });
});

function setTurnstileToken(token) {
    $('#turnstile-token').val(token);
}

const turnstileToken = $('#turnstile-token').val();

if (!turnstileToken) {
    Swal.fire({
        icon: 'error',
        title: 'Verifikasi Gagal',
        text: 'Silakan tunggu verifikasi keamanan selesai.',
    });
    return;
}

$(document).ajaxError(function (event, jqxhr) {
    if (jqxhr.status === 422) {
        // Jangan log apa pun untuk error validasi
        event.preventDefault();
        return false;
    }
});