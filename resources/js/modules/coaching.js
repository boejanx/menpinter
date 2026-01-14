// coaching.js
import { Modal, Tab } from 'bootstrap';
import { initDatepickers } from '@/plugins/datepicker.js';
import toastr from 'toastr';
import { formatTanggalIndonesia } from '@/helpers/tanggal.js';

window.formatTanggalIndonesia = formatTanggalIndonesia;

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// Fungsi bersih-bersih modal
function clearModalArtifacts() {
  $('body').removeClass('modal-open');
  $('.modal-backdrop').remove();
  $('body').css('overflow', 'auto');
}


export function destroyComent() {
  if ($.fn.DataTable.isDataTable('#coaching-table')) {
    $('#coaching-table').DataTable().destroy();
    $('#coaching-table').empty();
  }
}

export default function () {
  destroyComent();
  clearModalArtifacts();


  let table = $('#coaching-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '/coaching/show',
    order: [[1, 'asc']],
    columns: [
      { data: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'jenis' },
      { data: 'namakegiatan' },
      { data: 'institusi' },
      {
        data: 'tanggal_mulai',
        render: data => data ? formatDate(data) : ''
      },
      { data: 'status' },
      { data: 'action', orderable: false, searchable: false }
    ]
  });

  function formatDate(data) {
    const d = new Date(data);
    return `${String(d.getDate()).padStart(2, '0')}-${String(d.getMonth() + 1).padStart(2, '0')}-${d.getFullYear()}`;
  }

  $('#btnTambah').on('click', function () {
    resetForm();
    $('#backDropModalTitle').text('Tambah Riwayat Diklat');
    $('#form-coment').attr('action', '/coaching/store');
    const modal = new bootstrap.Modal(document.getElementById('backDropModal'));
    modal.show();
  });

  table.on('click', '.view-detail', function () {
    showDetail($(this).data('id'));
  });

  window.deleteCoaching = function (id) {
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Data ini akan dihapus dan tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '/coaching/' + id,
          type: 'DELETE',
          success: function (res) {
            if (res.status === 'success') {
              toastr.success(res.message);
              table.ajax.reload();
            } else {
              toastr.error(res.message || 'Gagal menghapus data.');
            }
          },
          error: function () {
            toastr.error('Terjadi kesalahan saat menghapus.');
          }
        });
      }
    });
  }

  $('#form-coment').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');
    const method = $('#formMethod').val();

    if (method !== 'PATCH' && !validateForm()) return;

    const formData = new FormData(form[0]);
    const docPelaksanaan = $('#docPelaksanaan')[0].files[0];
    const docEvaluasi = $('#docEvaluasi')[0].files[0];

    if (docPelaksanaan) formData.append('dokumen_pelaksanaan', docPelaksanaan);
    if (docEvaluasi) formData.append('dokumen_evaluasi', docEvaluasi);

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      beforeSend() {
        form.find('button[type=submit]').prop('disabled', true).text('Menyimpan...');
      },
      success(res) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('backDropModal')).hide();
        table.ajax.reload();
        resetForm();
        toastr.success(res.message || 'Data berhasil disimpan.');
      },
      error(xhr) {
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
        form.find('button[type=submit]').prop('disabled', false).text('Simpan');
      }
    });
  });

  $('#backDropModal').on('hidden.bs.modal', resetForm);

  table.on('click', '.btn-edit', function () {
    const id = $(this).data('id');

    $.ajax({
      url: `/coaching/get-detail/${id}`,
      type: 'GET',
      success: function (res) {
        if (!res || !res.id_usulan) {
          toastr.error('Data tidak ditemukan.');
          return;
        }

        $('#backDropModalTitle').text('Ubah Usulan Coaching/mentoring');
        $('#formMethod').val('PATCH');
        $('#form-coment').attr('action', `/coaching/store/${id}`);

        $('#jenisDiklat').val(res.id_diklat);
        $('#namaDiklat').val(res.namakegiatan);
        $('#institusiPenyelenggara').val(res.institusi);
        $('#tanggalMulai').val(res.tanggal_mulai+ ' - ' + res.tanggal_selesai);
        $('#tanggalSelesai').val(res.tanggal_selesai);

        $('#docPelaksanaan').val('');
        $('#docEvaluasi').val('');
        $('#form-coment .is-invalid').removeClass('is-invalid');

        const modal = new bootstrap.Modal(document.getElementById('backDropModal'));
        modal.show();
      },
      error: function () {
        toastr.error('Gagal mengambil data.');
      }
    });
  });

  function validateForm() {
    const requiredFields = ['#jenisDiklat', '#namaDiklat', '#institusiPenyelenggara', '#tanggalMulai', '#tanggalSelesai'];
    let valid = true;

    requiredFields.forEach(selector => {
      const field = $(selector);
      if (!field.val()?.trim()) {
        field.addClass('is-invalid');
        valid = false;
      } else {
        field.removeClass('is-invalid');
      }
    });

    const validateFile = (file, name) => {
      if (file && (file.type !== 'application/pdf' || file.size > 921600)) {
        toastr.error(`${name} harus berupa PDF dan maksimal 900KB.`);
        valid = false;
      }
    };

    validateFile($('#docPelaksanaan')[0].files[0], 'Dokumen pelaksanaan');
    validateFile($('#docEvaluasi')[0].files[0], 'Dokumen evaluasi');

    if (!valid) toastr.error('Mohon lengkapi semua kolom wajib dan periksa dokumen yang diunggah.');

    return valid;
  }

  function resetForm() {
    $('#form-coment')[0].reset();
    $('#formMethod').val('POST');
    $('#form-coment').attr('action', '/coaching/store');
    $('#form-coment .is-invalid').removeClass('is-invalid');
  }

  function showDetail(id) {
    $.ajax({
      url: `/coaching/get-detail/${id}`,
      method: 'GET',
      success: function (res) {
        $('#detailNamaKegiatan').text(res.namakegiatan);
        $('#detailJenis').text(res.jenis);
        $('#detailTanggalMulai').text(formatTanggalIndonesia(res.tanggal_mulai));
        $('#detailTanggalSelesai').text(formatTanggalIndonesia(res.tanggal_selesai));
        $('#detailStatus').text(res.status);

        $('#docPelaksanaanEmbed').attr('src', res.dokumen_pelaksanaan_url);
        $('#docEvaluasiEmbed').attr('src', res.dokumen_evaluasi_url);

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
      },
      error: function () {
        toastr.error('Terjadi kesalahan saat mengambil detail.');
      }
    });
  }

  $('#pelaksanaanTab, #evaluasiTab').on('click', function () {
    const target = $(this).attr('id') === 'pelaksanaanTab' ? '#docPelaksanaanEmbed' : '#docEvaluasiEmbed';
    if (!$(target).attr('src')) {
      toastr.warning(`Dokumen ${$(this).text()} tidak tersedia.`, 'Peringatan');
    }
  });

  $(document).on('click', '.btn-delete', function (e) {
    e.stopPropagation();
    $('.btn-delete').not(this).each(function () {
      bootstrap.Popover.getInstance(this)?.hide();
    });
    bootstrap.Popover.getInstance(this)?.toggle();
  });

  $(document).on('click', '.btn-ajukan', function (e) {
    e.stopPropagation();
    const id = $(this).data('id');
    Swal.fire({
      title: 'Ajukan Usulan?',
      text: "Apakah Anda yakin ingin mengajukan usulan ini?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Ajukan',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/coaching/ajukan/${id}`,
          type: 'POST',
          success: function (res) {
            toastr.success(res.message || 'Usulan berhasil diajukan.');
            table.ajax.reload();
          },
          error: function () {
            toastr.error('Gagal mengajukan usulan.');
          }
        });
      }
    });
  })

  initDatepickers();
}
