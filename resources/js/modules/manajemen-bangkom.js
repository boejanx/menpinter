import Swal from 'sweetalert2';
import { initDatepickers } from '@/plugins/datepicker.js';
import initQuillEditor from '../plugins/editor.js';


export default async function () {
    // Inisialisasi Quill Editor dan simpan instance-nya
    const quill = initQuillEditor('#editor', '#input', {
        placeholder: 'Isi deskripsi pelatihan utama...',
    });

    initDatepickers('[data-datepicker]', {
        maxDate: null,
        enableTime: true,
        minTime: "08:00",
        altFormat: "d/m/Y H:i",
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
    });

    const bangkomTableEl = $('#bangkomTable');
    if (!bangkomTableEl.length) return;

    // Ambil URL dari data attributes
    const ajaxUrl = bangkomTableEl.data('url');
    const storeUrl = bangkomTableEl.data('store-url');
    const csrfToken = bangkomTableEl.data('token');

    // Hancurkan instance DataTable yang ada untuk re-inisialisasi
    if ($.fn.dataTable.isDataTable(bangkomTableEl)) {
        bangkomTableEl.DataTable().destroy();
    }

    // Inisialisasi DataTable
    const table = bangkomTableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            className: 'text-center'
        },
        {
            data: 'event_tema',
            name: 'event_tema'
        },
        {
            data: 'waktu_pelaksanaan',
            name: 'waktu_pelaksanaan',
            orderable: false,
            searchable: false
        },
        {
            data: 'peserta_count',
            name: 'peserta_count',
            className: 'text-center'
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false,
            className: 'text-center'
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-center'
        },
        ]
    });

    // --- LOGIKA MODAL (SEKARANG LEBIH SEDERHANA) ---
    const eventModalEl = document.getElementById('tambahRiwayat');
    const eventModal = new bootstrap.Modal(eventModalEl);
    const eventForm = $('#eventForm');

    // Tombol 'Tambah Kegiatan'
    $('#addEventButton').on('click', function () {
        eventForm[0].reset();
        eventForm.find('input[name="_method"]').remove();
        eventForm.find('input[name="event_id"]').remove();
        $('#eventModalLabel').text('Tambah Kegiatan Baru');

        quill.setText('');

        // Kita hanya perlu mengosongkan tanggal, tidak perlu setting maxDate lagi
        const fpMulai = document.querySelector('#event_mulai')._flatpickr;
        const fpSelesai = document.querySelector('#event_selesai')._flatpickr;
        if (fpMulai) fpMulai.clear();
        if (fpSelesai) fpSelesai.clear();

        $('#imagePreviewWrapper').hide();
        eventForm.find('.is-invalid').removeClass('is-invalid');
        eventForm.find('.invalid-feedback').text('');
        eventModal.show();
    });

    // Tombol 'Edit' di dalam tabel
    bangkomTableEl.on('click', '.edit-btn', function (e) {
        e.preventDefault();
        const eventId = $(this).data('id');

        $.get(`/manja/${eventId}/edit`, function (data) {
            eventForm[0].reset();
            eventForm.find('input[name="_method"]').remove();
            eventForm.find('input[name="event_id"]').remove();
            eventForm.prepend('<input type="hidden" name="_method" value="PUT">');
            eventForm.prepend(`<input type="hidden" name="event_id" value="${data.event_id}">`);
            $('#eventModalLabel').text('Edit Kegiatan');

            $('#event_tema').val(data.event_tema);
            $('#event_lokasi').val(data.event_lokasi);
            $('#event_link').val(data.event_link);
            $('#event_jp').val(data.event_jp);
            quill.root.innerHTML = data.event_keterangan;

            const fpMulai = document.querySelector('#event_mulai')._flatpickr;
            const fpSelesai = document.querySelector('#event_selesai')._flatpickr;
            if (fpMulai) fpMulai.setDate(`${data.event_mulai}`, true);
            if (fpSelesai) fpSelesai.setDate(`${data.event_selesai}`, true);



            if (data.event_flyer) {
                $('#imagePreview').attr('src', data.event_flyer);
                $('#imagePreviewWrapper').show();
            } else {
                $('#imagePreviewWrapper').hide();
            }

            eventForm.find('.is-invalid').removeClass('is-invalid');
            eventForm.find('.invalid-feedback').text('');
            eventModal.show();
        }).fail(function () {
            Swal.fire('Gagal!', 'Tidak dapat mengambil data untuk diedit.', 'error');
        });
    });

    // Submit form untuk Tambah & Edit
    eventForm.on('submit', function (e) {
    e.preventDefault();

    // ⬅️ Pindahkan ini ke atas sebelum digunakan
    const formData = new FormData(this);

    // DEBUG: Lihat semua isi FormData
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    const method = $(this).find('input[name="_method"]').val() || 'POST';
    const eventId = $(this).find('input[name="event_id"]').val();
    let url = storeUrl;

    if (method === 'PUT') {
        url = `/manja/${eventId}`;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            eventModal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.success,
                timer: 1500,
                showConfirmButton: false
            });
            table.ajax.reload();
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                eventForm.find('.is-invalid').removeClass('is-invalid');
                eventForm.find('.invalid-feedback').text('');
                $.each(errors, function (key, value) {
                    const field = $('#' + key);
                    field.addClass('is-invalid');
                    field.closest('.mb-3, .col-md-6').find('.invalid-feedback').text(value[0]);
                });
            } else {
                Swal.fire('Gagal!', 'Terjadi kesalahan pada server.', 'error');
            }
        }
    });
});


    bangkomTableEl.on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        deleteData(id);
    });

    window.deleteData = function (id) {
        if (!id) return;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/manja/${id}`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: csrfToken
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: response.success,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                },
                error: function (xhr) {
                    let msg = 'Terjadi kesalahan saat menghapus data.';
                    if (xhr.responseJSON?.error) {
                        msg = xhr.responseJSON.error;
                    }
                    Swal.fire('Gagal!', msg, 'error');
                }
            });
        });
    };

    $('#event_flyer').on('change', function () {
        const file = this.files[0];
        const preview = $('#imagePreview');
        const wrapper = $('#imagePreviewWrapper');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.attr('src', e.target.result);
                wrapper.show();
            };
            reader.readAsDataURL(file);
        } else {
            preview.attr('src', '');
            wrapper.hide();
        }
    });

}