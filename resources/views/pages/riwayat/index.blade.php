<x-app-layout>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card bg-label-primary">
                <div class="card-body d-flex justify-content-between flex-wrap-reverse p-0">
                    <div class="px-4 py-4 mb-0 w-50 d-flex flex-column justify-content-between text-center text-sm-start">
                        <div class="card-title">
                            <h5 class="text-primary mb-0">Riwayat Pengembangan Kompetensi</h5>
                            <p class="text-body w-sm-80 app-academy-xl-100 d-none d-lg-block">
                                Berikut merupakan kegiatan yang pernah anda ikutkan dalam pengembangan kompetensi.
                            </p>
                            <div class="d-flex align-items-start column-gap-6 flex-sm-row flex-column row-gap-2">
                                <x-button class="btn btn-info" data-bs-target="#tambahRiwayat" data-bs-toggle="modal" icon="bx bx-plus" id="btn-tambah" text="Tambah Riwayat" variant="info" />
                                <x-button icon="bx bx-refresh-cw" id="btn-sync" text="Sync SIASN" variant="success" />
                            </div>
                        </div>
                    </div>
                    <div class="w-50 d-flex justify-content-center justify-content-sm-end h-px-150 mb-sm-0">
                        <img alt="riwayat" class="img-fluid" src="/assets/img/illustrations/history.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card p-1">
            <div class="table-responsive text-nowrap">
                <table class="table" id="historyTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Penyelenggara</th>
                            <th>JP</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal :hasForm="true" action="#" formId="form-tambah" centered id="tambahRiwayat" method="POST" staticBackdrop="static" title="Tambah Riwayat Bangkom">
        <x-form-select :options="[]" id="jenisDiklat" label="Jenis Diklat" name="jenisDiklat" placeholder="Pilih Jenis Diklat" />
        <div id="strukturalWrapper" style="display: none">
            <x-form-select :options="[]" id="strukturalSelect" label="Jenis Struktural" name="struktural_id" placeholder="Pilih Jenis Struktural" />
        </div>
        <input id="jenisKursusSertipikat" name="jenis_kursus_sertipikat" type="hidden" value="">
        <x-form-input id="namaDiklat" label="Nama Diklat" name="namaDiklat" placeholder="Masukkan Nama Diklat" />
        <x-form-input id="institusiPenyelenggara" label="Institusi Penyelenggara" name="institusiPenyelenggara" placeholder="Masukkan Institusi Penyelenggara" />
        <x-form-input id="nomorSertifikat" label="Nomor Sertifikat" name="nomorSertifikat" placeholder="Masukkan Nomor Sertifikat" />
        <div class="row">
            <div class="col-md-6">
                <x-form-input data-name="thn" data-datepicker="year" autocomplete="off"  id="tahunDiklat" label="Tahun Diklat" name="tahunDiklat" type="text" />
            </div>
            <div class="col-md-6">
                <x-form-input id="durasiJam" label="Durasi (Jam)" min="0" name="durasiJam" type="number" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-form-input data-name="tgl" id="tanggalMulai" autocomplete="off"  data-datepicker label="Tanggal Mulai" name="tanggalMulai" type="text" />
            </div>
            <div class="col-md-6">
                <x-form-input data-name="tgl" id="tanggalSelesai" autocomplete="off"  data-datepicker label="Tanggal Selesai" name="tanggalSelesai" type="text" />
            </div>
        </div>
        <x-form-file id="doc_sertifikat" label="Upload Dokumen Sertifikat" name="doc_sertifikat" />
    </x-modal>

    <!-- Modal -->
    <div aria-hidden="true" aria-labelledby="pdfModalLabel" class="modal modal-lg fade" id="pdfModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Dokumen Sertifikat</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <embed height="600px" id="pdfViewer" type="application/pdf" width="100%" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
