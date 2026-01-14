import toastr from 'toastr';

export default function () {

    $('#form-profil').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');
    const formData = form.serialize();

    $.ajax({
      url: url,
      method: 'PATCH',
      data: formData,
      beforeSend() {
        form.find('button[type=submit]').prop('disabled', true).text('Menyimpan...');
      },
      success(res) {
        toastr.success(res.message || 'Profil berhasil diperbarui.');
      },
      error(xhr) {
        form.find('.is-invalid').removeClass('is-invalid');

        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;
          for (let key in errors) {
            toastr.error(errors[key][0], 'Validasi Error');
            $(`[name="${key}"]`).addClass('is-invalid');
          }
        } else {
          toastr.error(xhr.responseJSON.message || 'Terjadi kesalahan server.', 'Gagal');
        }
      },
      complete() {
        form.find('button[type=submit]').prop('disabled', false).html('<i class="bx bx-save"></i> Simpan Perubahan');
      }
    });
  });
}