import { initDatepickers } from "@/plugins/datepicker.js";
import { formatTanggalIndonesia } from "@/helpers/tanggal.js";
import toastr from "toastr";
import Swal from "sweetalert2";

async function initVerifikasi() {
    // Hancurkan DataTable sebelumnya (jika ada)
    if ($.fn.dataTable.isDataTable("#verifikasiTable")) {
        $("#verifikasiTable").DataTable().clear().destroy();
    }

    // Inisialisasi DataTable
    const table = $("#verifikasiTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: "/verifikasi/data",
        columns: [
            { data: "jenis_diklat", name: "jenis_diklat" },
            { data: "biodata", name: "biodata", searchable: true },
            { data: "namakegiatan", name: "namakegiatan" },
            { data: "action", name: "action", orderable: false, searchable: false }
        ]
    });

    function hitung() {
        fetch("/verifikasi/hitung")
            .then(res => res.json())
            .then(data => {
                $("#setuju").text(data.setuju);
                $("#tolak").text(data.tolak);
                $("#belum").text(data.belum);
                $("#total").text(data.total);
                $("#persen-setuju").text("(" + data.persen_setuju + "%)");
                $("#persen-tolak").text("(" + data.persen_tolak + "%)");
                $("#persen-belum").text("(" + data.persen_belum + "%)");
            });
    }

    // Inisialisasi Select2 untuk Jenis Diklat
    $("#jenisDiklat").select2({
        dropdownParent: $("#tambahRiwayat"),
        placeholder: "Pilih Jenis Diklat",
        width: "100%",
        theme: "bootstrap-5",
        ajax: {
            url: "/api/diklat",
            dataType: "json",
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.jenis_diklat,
                        kursus: item.jenis_kursus_sertipikat
                    }))
                };
            }
        }
    });

    $("#verifikasiTable").on("click", ".view-detail", function () {
        const usulanId = $(this).data("id");

        fetch(`/verifikasi/detail/${usulanId}`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP error ' + res.status);
                return res.json();
            })
            .then(res => {
                const data = res.data;

                $("#setuju, #tolak").attr('data-id', data.id_usulan).data('id', data.id_usulan);
                $("#detailNamaKegiatan").text(data.namakegiatan ?? '-');
                $("#detailJenis").text(data.ref_bangkom?.jenis_diklat ?? '-');
                $("#detailTanggalMulai").text(
                    data.tanggal_mulai ? formatTanggalIndonesia(data.tanggal_mulai) : '-'
                );
                $("#detailTanggalSelesai").text(
                    data.tanggal_selesai ? formatTanggalIndonesia(data.tanggal_selesai) : '-'
                );
                $("#detailStatus").text(data.status ?? '-');

                // ================= RESET EMBED =================
                $('#docPelaksanaanEmbed').attr('src', '');
                $('#docEvaluasiEmbed').attr('src', '');
                $('#sertifikatEmbed').attr('src', '');

                // ================= JENIS DIKLAT =================
                const jenis = parseInt(data?.id_diklat);

                // ambil tombol tab (INI PENTING)
                const pelaksanaanBtn = document.querySelector('[data-bs-target="#tab-pelaksanaan"]');
                const evaluasiBtn = document.querySelector('[data-bs-target="#tab-evaluasi"]');
                const sertifikatBtn = document.querySelector('[data-bs-target="#tab-sertifikat"]');

                // ===== JENIS 14 & 15 → PELAKSANAAN + EVALUASI =====
                if ([14, 15].includes(jenis)) {

                    const docPelaksanaan = data.bangkom_doc?.doc_pelaksanaan;
                    const docEvaluasi = data.bangkom_doc?.doc_evaluasi;

                    if (docPelaksanaan) {
                        $('#docPelaksanaanEmbed').attr('src', `/storage/${docPelaksanaan}`);
                    }

                    if (docEvaluasi) {
                        $('#docEvaluasiEmbed').attr('src', `/storage/${docEvaluasi}`);
                    }

                    // tampilkan tab
                    $(pelaksanaanBtn).closest('li').show();
                    $(evaluasiBtn).closest('li').show();
                    $(sertifikatBtn).closest('li').hide();

                    // aktifkan tab pelaksanaan
                    if (pelaksanaanBtn) {
                        bootstrap.Tab.getOrCreateInstance(pelaksanaanBtn).show();
                    }

                }
                // ===== JENIS LAIN → SERTIFIKAT =====
                else {

                    const docSertifikat = data?.doc_sertifikat;

                    if (docSertifikat) {
                        $('#sertifikatEmbed').attr('src', `/storage/${docSertifikat}`);
                    }

                    $(pelaksanaanBtn).closest('li').hide();
                    $(evaluasiBtn).closest('li').hide();
                    $(sertifikatBtn).closest('li').show();

                    // aktifkan tab sertifikat
                    if (sertifikatBtn) {
                        bootstrap.Tab.getOrCreateInstance(sertifikatBtn).show();
                    }
                }

                // ================= SHOW MODAL =================
                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                modal.show();
            })

            .catch(error => {
                console.error('FETCH ERROR:', error);
                Swal.fire('Gagal', 'Gagal memuat detail usulan.', 'error');
            });
    });

    $('#detailModal').on('click', '#setuju', function () {
        const usulanId = $(this).data('id');
        Swal.fire({
            title: 'Setujui Usulan?',
            text: "Apakah Anda yakin ingin menyetujui usulan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/verifikasi/setuju/${usulanId}`,
                    type: 'POST',
                    success: function (res) {
                        toastr.success(res.message || 'Usulan berhasil disetujui.');
                        table.ajax.reload();
                        hitung();
                    },
                    error: function () {
                        toastr.error('Terjadi kesalahan saat menyetujui usulan.');
                    }
                });
            }
        });
    });

    $('#detailModal').on('click', '#tolak', function () {
    const usulanId = $(this).data('id');

    // TUTUP MODAL (Bootstrap 5)
    const modalEl = document.getElementById('detailModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    setTimeout(() => {
        Swal.fire({
            title: 'Tolak Usulan',
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: (alasan) => {
                if (!alasan || alasan.trim()) {
                    return alasan;
                }
                Swal.showValidationMessage('Alasan wajib diisi');
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/verifikasi/tolak/${usulanId}`,
                    type: 'POST',
                    data: {
                        alasan: result.value,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Usulan berhasil ditolak');
                        table.ajax.reload(null, false);
                        hitung();
                    },
                    error: function () {
                        toastr.error('Terjadi kesalahan saat menolak usulan');
                    }
                });
            }
        });
    }, 300);
});






    // reset pdf saat modal ditutup
    $('#detailModal').on('hidden.bs.modal', function () {
        $('#docPelaksanaanEmbed').attr('src', '');
        $('#docEvaluasiEmbed').attr('src', '');
        $('#sertifikatEmbed').attr('src', '');
    });

    // Tombol Tambah Riwayat
    window.handleTambah = () => {
        $("#tambahModal").modal("show");
        setTimeout(initSelects, 300);
    };

    $('#pelaksanaanTab, #evaluasiTab').on('click', function () {
        const target = $(this).attr('id') === 'pelaksanaanTab' ? '#docPelaksanaanEmbed' : '#docEvaluasiEmbed';
        if (!$(target).attr('src')) {
            toastr.warning(`Dokumen ${$(this).text()} tidak tersedia.`, 'Peringatan');
        }
    });

    $("#tambahModal").on("hidden.bs.modal", function () {
        $("#form-tambah")[0].reset();
        $("#strukturalWrapper").hide();
        $(".selectpicker").selectpicker("destroy");
    });


    window.handleSync = () => table.ajax.reload();

    // Inisialisasi datepicker
    initDatepickers();
    hitung();
}

export { initVerifikasi as default };
