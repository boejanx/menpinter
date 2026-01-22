<x-app-layout>
    <div class="row">
        <div class="col-lg-12 col-sm-12 mb-4">
            <div class="card bg-label-primary">
                <div class="card-body d-flex justify-content-between flex-wrap-reverse p-0">
                    <div class="px-4 py-4 mb-0 w-50 d-flex flex-column justify-content-between text-center text-sm-start">
                        <div class="card-title">
                            <h5 class="text-primary mb-0">Kursus / Pelatihan / Webinar</h5>
                            <p class="d-none text-body w-sm-80 app-academy-xl-100 d-lg-block">
                                Webinar adalah kegiatan pelatihan online yang dapat diikuti oleh pegawai untuk meningkatkan kompetensi dan pengetahuan. Pastikan Anda mengikuti webinar yang sesuai
                                dengan bidang Anda.
                            </p>
                        </div>
                    </div>
                    <div class="w-50 d-flex justify-content-center justify-content-sm-end h-px-150 mb-sm-0">
                        <img alt="webinar" class="img-fluid" src="/assets/img/illustrations/webinar.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="app-academy">
        <div class="card mb-6">
            <div class="card-header d-flex flex-wrap justify-content-between gap-4">
                <div class="card-title mb-0 me-1">
                    <h5 class="mb-0">Semua Bangkom</h5>
                    <p class="mb-0">Semua Kegiatan Bangkom yang tersedia</p>
                </div>
            </div>
            <div class="card-body" id="content">
                {{-- Ganti class 'row' menjadi seperti di bawah ini untuk mengatur jumlah kolom --}}
                @if ($events->total() === 0)
                    <div class="alert alert-warning text-center">
                        <i class="bx bx-info-circle me-1"></i>
                        Belum ada pelatihan yang tersedia
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 gy-6 mb-6" id="event-list">

                        @foreach ($events as $event)
                            {{-- Cukup gunakan class "col", karena jumlah kolom sudah diatur di parent-nya --}}
                            <div class="col">
                                <x-card bodyClass="p-2" class="p-2 h-100 shadow-none border p-2" outline="true" variant="bg-label-info">
                                    <img alt="Event Flyer" class="card-img-top lazy" data-src="{{ $event->event_flyer }}" src="{{ asset('assets/img/background/placeholder.png') }}"
                                        style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover; object-position: center;">

                                    <p class="h6">{{ $event->event_tema }}</p>
                                    <div class="row mb-4 g-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bx-calendar icon-lg"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-nowrap">@tanggal($event->event_mulai) - {{ $event->event_jam_mulai }}</h6>
                                                    <small>Tanggal Pelaksanaan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="d-flex align-items-center mb-1"><i class="icon-base bx bx-time-five me-1"></i>{{ $event->tanggal_mulai }}</p>
                                    <x-button class="w-100 btn btn-label-primary d-flex align-items-center menu-link" href="{{ route('bangkom', $event->event_id) }}" text="DETAIL KEGIATAN"
                                        variant="primary" />
                                </x-card>
                            </div>
                        @endforeach
                    </div>
                    @if ($events->hasMorePages())
                        <div class="d-flex justify-content-center mt-4">
                            <button class="btn btn-primary" data-next-page="{{ $events->currentPage() + 1 }}" id="load-more">
                                Load More
                            </button>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
