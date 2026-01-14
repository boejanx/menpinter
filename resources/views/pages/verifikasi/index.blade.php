<x-app-layout>
    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bxs-hourglass icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0" id="belum"></h4>
                        <p class="mb-2"> &nbsp; Perlu Diverifikasi</p>
                    </div>
                    <p class="mb-0">
                        <span class="text-heading fw-medium me-2" id="persen-belum">(%)</span>
                        <span class="text-body-secondary">Riwayat Bangkom perlu diverifikasi</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bxs-x icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0" id="tolak"></h4>
                        <p class="mb-2"> &nbsp; Ditolak</p>
                    </div>
                    <p class="mb-0">
                        <span class="text-heading fw-medium me-2" id="persen-tolak">(%)</span>
                        <span class="text-body-secondary">Riwayat Bangkom Ditolak</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bxs-check icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0" id="setuju"></h4>
                        <p class="mb-2"> &nbsp; Diterima</p>
                    </div>
                    <p class="mb-0">
                        <span class="text-heading fw-medium me-2" id="persen-setuju">(%)</span>
                        <span class="text-body-secondary">Riwayat Bangkom Diterima</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bxs-checks icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0" id="total"></h4>
                        <p class="mb-2"> &nbsp; Jumlah semua </p>
                    </div>
                    <p class="mb-0">
                        <span class="text-heading fw-medium me-2" id="persen-total">(100%)</span>
                        <span class="text-body-secondary">Jumlah semua</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card p-1">
            <div class="table-responsive text-nowrap">
                <table class="table table-responsive table-striped table-hover" id="verifikasiTable">
                    <thead>
                        <tr>
                            <th>Jenis Bangkom</th>
                            <th>NIP/Nama Pegawai</th>
                            <th>Nama Kegiatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">Detail Usulan Kegiatan</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>

                <div class="modal-body">

                    <!-- ================= INFO KEGIATAN ================= -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3 fw-semibold">Informasi Kegiatan</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted">Nama Kegiatan</small>
                                    <div class="fw-semibold" id="detailNamaKegiatan">-</div>
                                </div>

                                <div class="col-md-6">
                                    <small class="text-muted">Jenis Diklat</small>
                                    <div class="fw-semibold" id="detailJenis">-</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Tanggal Mulai</small>
                                    <div id="detailTanggalMulai">-</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Tanggal Selesai</small>
                                    <div id="detailTanggalSelesai">-</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">Status</small>
                                    <div class="badge bg-primary" id="detailStatus">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= TAB DOKUMEN ================= -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header p-0">
                            <ul class="nav nav-tabs nav-fill" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-target="#tab-pelaksanaan" data-bs-toggle="tab" type="button">
                                        Dokumen Pelaksanaan
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-target="#tab-evaluasi" data-bs-toggle="tab" type="button">
                                        Dokumen Evaluasi
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-target="#tab-sertifikat" data-bs-toggle="tab" type="button">
                                        Dokumen Sertifikat
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-0">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab-pelaksanaan">
                                    <embed height="500px" id="docPelaksanaanEmbed" src="" type="application/pdf" width="100%">
                                </div>

                                <div class="tab-pane fade" id="tab-evaluasi">
                                    <embed height="500px" id="docEvaluasiEmbed" src="" type="application/pdf" width="100%">
                                </div>

                                <div class="tab-pane fade" id="tab-sertifikat">
                                    <embed height="500px" id="sertifikatEmbed" src="" type="application/pdf" width="100%">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer mt-4">
                    <button class="btn btn-success" data-id="" id="setuju" type="button">Setuju & Sync ke SIASN</button>
                    <button class="btn btn-danger" data-id="" id="tolak" type="button">Tolak</button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
