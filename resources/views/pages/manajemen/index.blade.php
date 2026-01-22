<x-app-layout>
    <div class="row g-6 mb-6">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bxs-calendar-event icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $stats['total_event'] ?? '0' }}</h4>
                    </div>
                    <p class="mb-2">Total Kegiatan</p>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success"><i class="icon-base bx bx-play-circle icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $stats['event_berlangsung'] ?? '0' }}</h4>
                    </div>
                    <p class="mb-2">Berlangsung</p>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-time-five icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $stats['event_akan_datang'] ?? '0' }}</h4>
                    </div>
                    <p class="mb-2">Akan Datang</p>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="icon-base bx bx-check-circle icon-lg"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $stats['event_selesai'] ?? '0' }}</h4>
                    </div>
                    <p class="mb-2">Telah Selesai</p>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="ps-0">Daftar Kegiatan</h5>
                    <button class="btn btn-primary" id="addEventButton">
                        <i class="bx bx-plus me-1"></i> Tambah Kegiatan Pengembangan Kompetensi
                    </button>
                </div>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" data-store-url="{{ route('manja.store') }}" data-url="{{ route('manajemen_bangkom.getData') }}" id="bangkomTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Waktu Pelaksanaan</th>
                            <th>Jml Peserta</th>
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

    <div aria-hidden="true" aria-labelledby="tambahRiwayatLabel" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="tambahRiwayat" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahRiwayatLabel">Tambah Kegiatan Baru</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                {{-- ID form harus "addEventForm" sesuai dengan JS --}}
                <form enctype="multipart/form-data" id="eventForm" method="POST" novalidate>
                    <div class="modal-body">
                        {{-- Nama Kegiatan --}}
                        <div class="mb-3">
                            <label class="form-label" for="event_tema">Nama Kegiatan/Tema</label>
                            <input class="form-control" id="event_tema" name="event_tema" required type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3" id="editor" style="height: 200px;"></div>
                        <input id="input" name="event_keterangan" type="hidden">

                        {{-- Tanggal Mulai & Selesai --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="event_mulai">Tanggal Mulai</label>
                                <input class="form-control" data-datepicker id="event_mulai" name="event_mulai"  readonly required type="text">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="event_selesai">Tanggal Selesai</label>
                                <input class="form-control" data-datepicker id="event_selesai" name="event_selesai" readonly required type="text">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div class="mb-3">
                            <label class="form-label" for="event_lokasi">Lokasi</label>
                            <input class="form-control" id="event_lokasi" name="event_lokasi" type="text">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Link --}}
                        <div class="mb-3">
                            <label class="form-label" for="event_link">Link (Zoom/GMeet, dll)</label>
                            <input class="form-control" id="event_link" name="event_link" placeholder="https://example.com" type="url">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Jam Pelajaran --}}
                        <div class="mb-3">
                            <label class="form-label" for="event_jp">Jumlah Jam Pelajaran (JP)</label>
                            <input class="form-control" id="event_jp" min="0" name="event_jp" type="number">
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Input File Flyer/Banner -->
                        <div class="mb-3">
                            <label class="form-label" for="event_flyer">Flyer/Banner</label>
                            <input accept="image/*" class="form-control" id="event_flyer" name="event_flyer" type="file">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Wrapper untuk preview gambar yang dikontrol oleh JS -->
                        <div class="mb-3" id="imagePreviewWrapper" style="display: none;">
                            <label class="form-label">Preview Banner Saat Ini:</label>
                            <img alt="Image Preview" class="img-fluid rounded" id="imagePreview" src="" style="max-height: 200px;">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
