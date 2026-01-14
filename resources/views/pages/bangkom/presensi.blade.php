<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Presensi Kegiatan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('presensi', $bangkom->event_id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">NIP / NIK</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->nip ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->jabatan ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->unit_kerja ?? '-' }}" readonly>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success">Isi Kehadiran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
