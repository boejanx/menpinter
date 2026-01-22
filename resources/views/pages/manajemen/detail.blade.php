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
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-header ps-0">Daftar Kegiatan</h5>
                <button class="btn btn-primary" id="addEventButton">
                    <i class="bx bx-plus me-1"></i> Tambah Kegiatan
                </button>
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

</x-app-layout>
