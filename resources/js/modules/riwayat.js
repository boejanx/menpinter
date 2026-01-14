import { initDatepickers } from "@/plugins/datepicker.js";
import Swal from "sweetalert2";

async function initRiwayat() {
    if ($.fn.dataTable.isDataTable("#historyTable")) {
        $("#historyTable").DataTable().clear().destroy();
    }

    const table = $("#historyTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: "/riwayat/data",
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "namakegiatan", name: "namakegiatan" },
            { data: "institusi", name: "institusi" },
            { data: "jumlah_jp", name: "jumlah_jp" },
            { data: "tahun", name: "tahun" },
            { data: "status", name: "status" },
            { data: "actions", name: "actions", orderable: false, searchable: false }
        ]
    });

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

    $("#jenisDiklat").on("select2:select", function (e) {
        const data = e.params.data;
        $("#jenisKursusSertipikat").val(data.kursus);

        if (data.id == 1) {
            $("#strukturalWrapper").removeClass("hidden").show();

            if ($.fn.select2 && $("#strukturalSelect").hasClass("select2-hidden-accessible")) {
                $("#strukturalSelect").select2("destroy");
            }

            $("#strukturalSelect").select2({
                dropdownParent: $("#tambahRiwayat"),
                placeholder: "Pilih Jenis Struktural",
                width: "100%",
                theme: "bootstrap-5",
                ajax: {
                    url: "/api/struktural",
                    dataType: "json",
                    processResults: function (res) {
                        return {
                            results: res.map(item => ({
                                id: item.id,
                                text: item.nama
                            }))
                        };
                    }
                }
            });
        } else {
            $("#strukturalWrapper").hide();
            $("#strukturalSelect").val(null).trigger("change");
        }
    });

    window.handleTambah = () => {
        $("#tambahRiwayat").modal("show");
        setTimeout(initSelects, 300);
    };

    $("#form-tambah").on("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        formData.set("jenisDiklat", $("#jenisDiklat").val());
        formData.set("strukturalSelect", $("#strukturalSelect").val());

        fetch("riwayat/simpan", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tampilkan SweetAlert untuk sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Optional: Tambahkan aksi setelah alert ditutup
                        if (result.isConfirmed) {
                            // Misalnya: reset form, redirect, atau reload halaman
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('tambahRiwayat')).hide();
                            table.ajax.reload();
                            // window.location.reload();
                        }
                    });
                } else {
                    // Tampilkan SweetAlert untuk error
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Cek Kembali Isian, Semua Field Wajib Diisi',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada sistem',
                    confirmButtonText: 'OK'
                });
            });
    });

    $("#btn-sync").on("click", function () {
    Swal.fire({
        title: "Sinkronisasi Data",
        text: "Apakah Anda yakin ingin menyinkronkan data riwayat pengembangan kompetensi ke SIASN?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Sinkronkan",
        cancelButtonText: "Batal"
    }).then(result => {
        if (result.isConfirmed) {
            // Tampilkan modal dengan progress indicator
            let progress = 0;
            const progressInterval = setInterval(() => {
                if (progress < 90) {
                    progress += Math.random() * 10;
                    if (progress > 90) progress = 90;
                    if (document.getElementById("progress-bar")) {
                        document.getElementById("progress-bar").style.width = progress + "%";
                    }
                }
            }, 500);

            const swalInstance = Swal.fire({
                title: "Memulai Sinkronisasi...",
                html: `
                <div style="width:100%; background:#eee; border-radius:8px; overflow:hidden; margin-top:10px;">
                    <div id="progress-bar" style="
                        width:0%;
                        height:10px;
                        background:#3085d6;
                        transition: width 0.4s ease;
                    "></div>
                </div>
                <p id="sync-status" style="margin-top:8px; font-size:13px; color:#666;">
                    Mengirim permintaan sinkronisasi...
                </p>
                <p id="sync-detail" style="margin-top:4px; font-size:12px; color:#888;"></p>
            `,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    // Kirim request untuk memulai sinkronisasi
                    fetch("/riwayat/sync", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Job berhasil di-dispatch
                            document.getElementById("sync-status").innerHTML = 
                                data.message;
                            document.getElementById("sync-detail").innerHTML = 
                                "Proses berjalan di background. Halaman ini dapat ditutup.";
                            
                            // Sembunyikan progress bar
                            clearInterval(progressInterval);
                            document.getElementById("progress-bar").style.width = "100%";
                            
                            // Auto close setelah 3 detik
                            setTimeout(() => {
                                swalInstance.close();
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: data.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    // Reload tabel setelah sinkronisasi (opsional)
                                    // table.ajax.reload();
                                });
                            }, 3000);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        clearInterval(progressInterval);
                        swalInstance.close();
                        Swal.fire({
                            title: "Gagal",
                            text: "Terjadi kesalahan: " + error.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    });
                }
            });
        }
    });
});

// Fungsi untuk polling status (opsional)
function startSyncPolling(jobId) {
    let pollingCount = 0;
    const maxPolling = 60; // Maksimal 5 menit (60x5 detik)
    
    const poll = setInterval(() => {
        pollingCount++;
        
        fetch(`/riwayat/sync-status?job_id=${jobId}`)
            .then(res => res.json())
            .then(data => {
                if (data.completed || pollingCount >= maxPolling) {
                    clearInterval(poll);
                    if (data.success) {
                        showSyncComplete(data);
                    }
                }
            })
            .catch(() => {
                clearInterval(poll);
            });
    }, 5000); // Poll setiap 5 detik
}


    $("#tambahRiwayat").on("hidden.bs.modal", function () {
        $("#form-tambah")[0].reset();
        $("#strukturalWrapper").hide();
        $(".selectpicker").selectpicker("destroy");
    });

    window.handleSync = () => table.ajax.reload();

    // Inisialisasi datepicker
    initDatepickers();
}

export { initRiwayat as default };
