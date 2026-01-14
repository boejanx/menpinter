<x-app-layout>
    <div class="row g-6">
        <div class="col-lg-12">
            <h5 class="card-title mb-0"><i class="bx bx-home"></i> Detail Pelatihan</h5>
        </div>
        <div class="col-lg-8">
            <x-card>

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-2">
                    <div class="me-1">
                        <h5 class="mb-0">{{ $bangkom->event_tema }}</h5>
                        <p class="mb-0">{{ $bangkom->event_lokasi }}</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-danger">Kepegawaian</span>
                        <i class="icon-base bx bx-share-alt icon-lg mx-4"></i>
                        <i class="icon-base bx bx-bookmarks icon-lg"></i>
                    </div>
                </div>
                <x-card class="academy-content shadow-none border">
                    {!! $bangkom->event_keterangan !!}

                    <hr class="my-6" />
                    <h5>Narasumber</h5>
                    <div class="d-flex justify-content-start align-items-center user-name">
                        <div class="avatar-wrapper">
                            <div class="avatar me-4"><img alt="Avatar" class="rounded-circle" src="../../assets/img/avatars/11.png" /></div>
                        </div>
                        <div class="d-flex flex-column">
                            <h6 class="mb-1">Devonne Wallbridge</h6>
                            <small>Web Developer, Designer, and Teacher</small>
                        </div>
                    </div>
                    <hr class="my-6" />
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <x-button class="btn bg-blue d-flex align-items-center" href="{{ $bangkom->event_link }}" icon="base-icon fa fa-video" text="Join Zoom Meeting" variant="primary">
                        </x-button>
                        <div class="sharethis-inline-share-buttons"></div>
                        <x-button class="btn btn-youtube d-flex align-items-center" href="https://www.youtube.com/@bkpsdm_kabpkl" icon="base-icon fa fa-play" text="Stream via Youtube" variant="danger"></x-button>
                    </div>

                </x-card>
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card bodyClass="pt-0 px-0" class="h-100">
                <div class="px-5 py-4 bg-light">
                    <div class="text-center align-items-center">
                        <p class="mb-0 text-uppercase fw-bold">Informasi Kegiatan</p>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-4">
                        <span class="fw-semibold">Tanggal Pelaksanaan</span>
                        <span class="text-body">@tanggal($bangkom->event_mulai)</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-4">
                        <span class="fw-semibold">Waktu Pelaksanaan</span>
                        <span class="text-body">{{ $bangkom->event_jam_mulai }} - {{ $bangkom->event_jam_selesai }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-4">
                        <span class="fw-semibold">Lokasi</span>
                        <span class="text-body">{{ $bangkom->event_lokasi }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-4">
                        <span class="fw-semibold">Peserta Terdaftar</span>
                        <span class="text-body">{{ $jumlahPeserta }}</span>
                    </li>
                </ul>

                @php
                    $sekarang = now();
                @endphp

                {{-- Event Selesai --}}
                @if ($sekarang->greaterThan($bangkom->event_selesai))
                    <div class="px-4">
                        <div class="alert alert-danger w-100" disabled>Kegiatan Selesai</div>
                    </div>

                    {{-- Sudah Terdaftar --}}
                @elseif ($sudahTerdaftar)
                    <div class="px-5 py-4 bg-light">
                        <div class="text-center justify-content-between align-items-center">
                            <p class="mb-0 text-uppercase fw-bold">Anda Sudah Terdaftar</p>
                        </div>
                    </div>

                    {{-- Sudah Presensi --}}
                    @if ($peserta && $peserta->presensi_at)
                        <div class="px-4">
                            <x-button class="btn btn-success w-100 mt-4" disabled text="✅ Sudah Hadir" />
                        </div>

                        {{-- Sudah waktunya presensi --}}
                    @elseif (now()->greaterThanOrEqualTo($bangkom->event_mulai))
                        <div class="px-4">
                            <x-button data-url="{{route('presensi', $bangkom->event_id)}}" class="btn btn-info w-100 mt-4 btn-presensi" variant="success" icon="icon-base fa fa-user" id="isi-kehadiran" text="Isi Kehadiran" ></x-button>
                        </div>

                        {{-- Belum waktunya presensi --}}
                    @else
                        <div class="px-4">
                            <x-button class="btn btn-secondary w-100 mt-4" disabled>
                                Presensi Belum Tersedia
                            </x-button>
                            <p class="alert alert-info text-center bold mt-2">
                                Presensi akan dibuka saat kegiatan dimulai
                            </p>
                        </div>
                    @endif

                    {{-- Belum Terdaftar --}}
                @else
                    <form action="{{ route('bangkom.daftar', $bangkom->event_id) }}" id="formDaftarBangkom" method="POST" name="daftarBangkom">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center mt-4 px-4">
                            <x-button class="btn-block btn-label-info w-100" icon="base-icon bx bx-video" id="daftar-bangkom" text="Ikuti Kegiatan" variant="primary"></x-button>
                        </div>
                    </form>
                @endif

                {{-- Tombol Sertifikat --}}
                @if ($peserta && $peserta->presensi_at && now()->greaterThan($bangkom->event_selesai))
                    <div class="px-4">
                        <a class="btn btn-success w-100 mt-3" href="{{ route('bangkom.sertifikat', $peserta->id_participant) }}" target="_blank">
                            <i class="bx bx-download"></i> Unduh Sertifikat
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
