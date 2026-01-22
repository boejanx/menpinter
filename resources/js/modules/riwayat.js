import { initDatepickers } from "@/plugins/datepicker.js";
import Swal from "sweetalert2";

/**
 * =====================================================
 * MAIN INIT (SPA SAFE)
 * =====================================================
 */
export default function initRiwayat() {

    /**
     * ===============================
     * INIT SELECT2 GLOBAL
     * ===============================
     */
    if (typeof initSelect2 === "function") {
        initSelect2();
    }

    /**
     * ===============================
     * DATATABLE INIT (SAFE DESTROY)
     * ===============================
     */
    if ($.fn.DataTable.isDataTable("#historyTable")) {
        $("#historyTable").DataTable().destroy();
    }

    const table = $("#historyTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "/riwayat/data",
        columns: [
            { data: "DT_RowIndex", orderable: false, searchable: false },
            { data: "namakegiatan" },
            { data: "institusi" },
            { data: "jumlah_jp" },
            { data: "tahun" },
            { data: "status" },
            { data: "actions", orderable: false, searchable: false }
        ]
    });

    /**
     * ===============================
     * JENIS DIKLAT SELECT2
     * ===============================
     */
    if (!$("#jenisDiklat").hasClass("select2-hidden-accessible")) {
        $("#jenisDiklat").select2({
            dropdownParent: $("#tambahRiwayat"),
            placeholder: "Pilih Jenis Diklat",
            width: "100%",
            theme: "bootstrap-5",
            ajax: {
                url: "/api/diklat",
                dataType: "json",
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.id,
                        text: item.jenis_diklat,
                        kursus: item.jenis_kursus_sertipikat
                    }))
                })
            }
        });
    }

    /**
     * ===============================
     * JENIS DIKLAT CHANGE
     * ===============================
     */
    $(document)
        .off("select2:select", "#jenisDiklat")
        .on("select2:select", "#jenisDiklat", function (e) {
            const data = e.params.data;
            $("#jenisKursusSertipikat").val(data.kursus);

            if (data.id == 1) {
                $("#strukturalWrapper").removeClass("hidden").show();

                if ($("#strukturalSelect").hasClass("select2-hidden-accessible")) {
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
                        processResults: res => ({
                            results: res.map(item => ({
                                id: item.id,
                                text: item.nama
                            }))
                        })
                    }
                });
            } else {
                $("#strukturalWrapper").hide();
                $("#strukturalSelect").val(null).trigger("change");
            }
        });

    /**
     * ===============================
     * SHOW MODAL TAMBAH
     * ===============================
     */
    window.handleTambah = () => {
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById("tambahRiwayat")
        ).show();
    };

    /**
     * ===============================
     * FORM SUBMIT (ANTI DOUBLE)
     * ===============================
     */
    $(document)
        .off("submit", "#form-tambah")
        .on("submit", "#form-tambah", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.set("jenisDiklat", $("#jenisDiklat").val());
            formData.set("strukturalSelect", $("#strukturalSelect").val());

            fetch("/riwayat/simpan", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content")
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Sukses",
                            text: data.message,
                        }).then(() => {
                            bootstrap.Modal
                                .getOrCreateInstance(
                                    document.getElementById("tambahRiwayat")
                                )
                                .hide();
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: data.message || "Cek kembali isian",
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Terjadi kesalahan sistem",
                    });
                });
        });

    /**
     * ===============================
     * SYNC BUTTON
     * ===============================
     */
    $(document)
        .off("click", "#btn-sync")
        .on("click", "#btn-sync", function () {
            Swal.fire({
                title: "Sinkronisasi Data",
                text: "Sinkronkan data ke SIASN?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: "Memulai Sinkronisasi...",
                    html: `
                        <div style="width:100%;background:#eee;border-radius:6px">
                            <div id="progress-bar" style="width:0;height:10px;background:#3085d6"></div>
                        </div>
                        <p style="margin-top:10px;font-size:13px">Proses berjalan di background</p>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        fetch("/riwayat/sync", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content")
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                Swal.close();
                                Swal.fire({
                                    icon: data.success ? "success" : "error",
                                    title: data.success ? "Berhasil" : "Gagal",
                                    text: data.message,
                                });
                            })
                            .catch(() => {
                                Swal.close();
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Gagal memulai sinkronisasi",
                                });
                            });
                    }
                });
            });
        });

    /**
     * ===============================
     * MODAL CLEANUP
     * ===============================
     */
    $("#tambahRiwayat")
        .off("hidden.bs.modal")
        .on("hidden.bs.modal", function () {
            this.querySelector("form")?.reset();
            $("#strukturalWrapper").hide();

            if ($("#strukturalSelect").hasClass("select2-hidden-accessible")) {
                $("#strukturalSelect").select2("destroy");
            }
        });

    /**
     * ===============================
     * DATEPICKER
     * ===============================
     */
    initDatepickers();
}
