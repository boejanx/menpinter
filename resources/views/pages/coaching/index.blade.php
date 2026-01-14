<x-app-layout>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card bg-label-primary">
                <div class="card-body d-flex justify-content-between flex-wrap-reverse p-0">
                    <div class="px-4 py-4 mb-0 w-50 d-flex flex-column justify-content-between text-center text-sm-start">
                        <div class="card-title">
                            <h5 class="text-primary mb-0">Coaching & Mentoring</h5>
                            <p class="text-body w-sm-80 app-academy-xl-100">
                                Selamat datang di aplikasi pengembangan kompetensi ASN Pemerintah Kabupaten Pekalongan
                            </p>
                            <a class="btn btn-primary mb-1" href="javascript:;" id="btnTambah">Tambah Coaching/Mentoring</a>
                        </div>
                    </div>
                    <div class="w-50 d-flex justify-content-center justify-content-sm-end h-px-150 mb-sm-0">
                        <img alt="coaching" class="img-fluid" src="/assets/img/illustrations/coaching.png" >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card">
            <div class="table-responsive text-nowrap py-4">
                <table class="table table-hover table-striped" id="coaching-table">
                    <thead>
                        <tr>
                           <th width="2%">No</th>
                           <th width="10%">Jenis</th>
                           <th>Nama Kegiatan</th>
                           <th>Lokasi</th>
                           <th>Tanggal</th>
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
    <div aria-hidden="true" class="modal fade" data-bs-backdrop="static" id="backDropModal" style="display: none;" tabindex="-1">
        <div class="modal-dialog">
            <form action="/coment/store" class="modal-content" enctype="multipart/form-data" id="form-coment" method="POST">
                @csrf
                <input id="formMethod" name="_method" type="hidden" value="POST"> <!-- Hidden field to determine the form method -->
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Tambah/Ubah Riwayat Diklat</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <!-- Jenis Diklat -->
                    <div class="row">
                        <div class="col mb-4 mt-2">
                            <div class="form-floating form-floating-outline">
                                <select aria-label="Jenis Diklat" class="form-select" id="jenisDiklat" name="jenis_diklat">
                                    <option selected="">Pilih Kegiatan</option>
                                    <option value="14">Coaching</option>
                                    <option value="15">Mentoring</option>
                                </select>
                                <label for="jenisDiklat">Jenis Kagiatan</label>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Diklat -->
                    <div class="row">
                        <div class="col mb-4 mt-2">
                            <div class="form-floating form-floating-outline" id="namaDiklatContainer">
                                <input autocomplete="off" class="form-control" id="namaDiklat" name="nama_diklat" placeholder="Masukkan Nama Diklat" type="text" value="">
                                <label for="namaDiklat">Materi Coaching/Mentoring</label>
                            </div>
                        </div>
                    </div>

                    <!-- Institusi Penyelenggara -->
                    <div class="row">
                        <div class="col mb-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input autocomplete="off" class="form-control" id="institusiPenyelenggara" name="institusi_penyelenggara" placeholder="Masukkan Institusi Penyelenggara" type="text"
                                    value="">
                                <label for="institusiPenyelenggara">Unit Kerja Penyelenggara</label>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Mulai dan Tanggal Selesai -->
                    <div class="row g-4">
                        <div class="col-md-6 mb-4 mt-4">
                            <div class="form-floating form-floating-outline">
                                <input autocomplete="off" class="form-control" data-datepicker id="tanggalMulai" name="tanggal_mulai" placeholder="Pilih tanggal" type="text" value="">
                                <label for="tanggalMulai">Tanggal Mulai</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 mt-4">
                            <div class="form-floating form-floating-outline">
                                <input autocomplete="off" class="form-control" data-datepicker id="tanggalSelesai" name="tanggal_selesai" placeholder="Pilih tanggal" type="text" value="">
                                <label for="tanggalSelesai">Tanggal Selesai</label>
                            </div>
                        </div>
                    </div>
                    <!-- Dokumen Pelaksanaan -->
                    <div class="row">
                        <div class="col mb-4 mt-2">
                            <label class="form-label" for="dokumenPelaksanaan">Dokumen Pelaksanaan</label>
                            <input accept=".pdf" class="form-control" id="docPelaksanaan" name="dokumen_pelaksanaan" type="file">
                        </div>
                    </div>

                    <!-- Dokumen Evaluasi -->
                    <div class="row">
                        <div class="col mb-4 mt-2">
                            <label class="form-label" for="dokumenEvaluasi">Dokumen Evaluasi</label>
                            <input accept=".pdf" class="form-control" id="docEvaluasi" name="dokumen_evaluasi" type="file">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Menampilkan Detail Usulan dan Dokumen -->
    <div aria-hidden="true" aria-labelledby="detailModalLabel" class="modal fade modal-xl" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Usulan</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <!-- Detail Usulan -->
                    <div class="container px-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ri-booklet-line fs-3 text-primary me-3"></i>
                                        <div>
                                            <h6 class="card-title mb-1">Nama Kegiatan</h6>
                                            <p class="card-text text-muted" id="detailNamaKegiatan">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ri-shapes-line fs-3 text-success me-3"></i>
                                        <div>
                                            <h6 class="card-title mb-1">Jenis</h6>
                                            <p class="card-text text-muted" id="detailJenis">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ri-calendar-line fs-3 text-info me-3"></i>
                                        <div>
                                            <h6 class="card-title mb-1">Tanggal Mulai</h6>
                                            <p class="card-text text-muted" id="detailTanggalMulai">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ri-calendar-check-line fs-3 text-warning me-3"></i>
                                        <div>
                                            <h6 class="card-title mb-1">Tanggal Selesai</h6>
                                            <p class="card-text text-muted" id="detailTanggalSelesai">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="ri-checkbox-circle-line fs-3 text-danger me-3"></i>
                                        <div>
                                            <h6 class="card-title mb-1">Status</h6>
                                            <p class="card-text text-muted" id="detailStatus">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header px-0 pt-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button aria-controls="navs-justified-home" aria-selected="true" class="nav-link active waves-effect" data-bs-target="#navs-justified-home"
                                            data-bs-toggle="tab" role="tab" type="button"><span class="d-none d-sm-block"><i class="tf-icons ri-home-smile-line me-1_5"></i> Dokumen Pelaksanaan </span><i
                                                class="ri-home-smile-line ri-20px d-sm-none"></i></button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button aria-controls="navs-justified-profile" aria-selected="false" class="nav-link waves-effect" data-bs-target="#navs-justified-profile"
                                            data-bs-toggle="tab" role="tab" tabindex="-1" type="button"><span class="d-none d-sm-block"><i class="tf-icons ri-user-3-line me-1_5"></i>
                                                Dokumen Evaluasi</span><i class="ri-user-3-line ri-20px d-sm-none"></i></button>
                                    </li>
                                    <span class="tab-slider" style="left: 0px; width: 236.359px; bottom: 0px;"></span>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                                    <embed height="500px" id="docPelaksanaanEmbed" src="" type="application/pdf" width="100%" />
                                </div>
                                <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                                    <embed height="500px" id="docEvaluasiEmbed" src="" type="application/pdf" width="100%" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
