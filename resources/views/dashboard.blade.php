<x-app-layout>

    <div class="row g-6">
        <div class="col-lg-6 col-sm-12">
            <div class="card bg-label-primary">
                <div class="card-body d-flex justify-content-between flex-wrap-reverse p-0">
                    <div class="px-4 py-4 mb-0 w-50 d-flex flex-column justify-content-between text-center text-sm-start">
                        <div class="card-title">
                            <h5 class="text-primary mb-2">Selamat Datang, {{ Auth::user()->name }}!</h5>
                            <p class="text-body w-sm-80 app-academy-xl-100">
                                di aplikasi pengembangan kompetensi ASN Pemerintah Kabupaten Pekalongan
                            </p>
                        </div>
                    </div>
                    <div class="w-50 d-flex justify-content-center justify-content-sm-end h-px-200 mb-sm-0">
                        <img alt="boy illustration" class="img-fluid scaleX-n1-rtl" src="/assets/img/illustrations/dashboard.png">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-12">
            <div class="row">
                <div class="col-lg-6 col-sm-6 mb-4 col-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="icon-base bx bxs-reading icon-lg"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $rwjp->jp_8 ?? '0' }}</h4>
                            </div>
                            <p class="m-0">Coaching</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 mb-4 col-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning"><i class="icon-base bx bx-user-check icon-lg"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $rwjp->jp_9 ?? '0' }}</h4>
                            </div>
                            <p class="m-0">Mentoring</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 mb-4 col-6">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-danger"><i class="icon-base bx bx-reading icon-lg"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $rwjp->jp_lainnya ?? '0' }}</h4>
                            </div>
                            <p class="m-0">Diklat dan Pelatihan</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6   mb-4 col-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-info"><i class="icon-base bx bx-calendar icon-lg"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $rwjp->jp_total ?? '0' }}</h4>
                            </div>
                            <p class="m-0">Total JP Tahun Berjalan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- On route vehicles Table -->
        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-12 order-4">
            <div class="card card-border-shadow-info">
                <table class="table align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Agenda Pelatihan</th>
                                </tr>
                            </thead>
                </table>

                <ul class="list-group list-group-flush">
                    @forelse ($upcoming as $agenda)
                        <li class="list-group-item p-0">
                            <a class="d-block text-decoration-none text-dark menu-link p-3" href="{{ route('bangkom', $agenda->event_id) }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $agenda->event_tema }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bx bx-calendar me-1"></i>
                                            @tanggal($agenda->event_mulai)
                                        </small>
                                    </div>

                                    @if (\Carbon\Carbon::parse($agenda->event_mulai)->diffInDays(now()) <= 7)
                                        <span class="badge bg-warning">
                                            Segera
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">
                            Tidak ada agenda pelatihan yang akan datang
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>
        <div class="col-md-8 col-lg-8 col-xs-12 col-sm-12 order-5">
            <div class="card card-border-shadow-primary">
                <h5 class="card-header">Pelatihan Yang Anda Ikuti</h5>
                <div class="table-responsive text-nowrap">

                    @if ($pelatihan->count())
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Kegiatan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody class="table-border-bottom-0">
                                @foreach ($pelatihan as $item)
                                    @php
                                        $mulai = $item->bangkom->event_mulai;
                                        $selesai = $item->bangkom->event_selesai;
                                    @endphp
                                    <tr>
                                        <td>
                                            <a class="menu-link fw-semibold" href="{{ route('bangkom', $item->bangkom->event_id) }}">
                                                {{ $item->bangkom->event_tema }}
                                            </a>
                                        </td>

                                        <td>
                                            @tanggal($mulai)
                                        </td>

                                        <td>
                                            <span
                                                class="badge 
                                @if ($item->bangkom->status === 'akan_datang') bg-label-info
                                @elseif ($item->bangkom->status === 'selesai') bg-label-danger
                                @else bg-label-success @endif">
                                                {{ str_replace('_', ' ', ucwords($item->bangkom->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning text-dark text-center">
                            <i class="bx bx-info-circle me-1"></i>
                            <h5>Belum ada pelatihan yang anda ikuti</h5>
                        </div>
                    @endif

                </div>

            </div>
        </div>
        <!--/ On route vehicles Table -->
    </div>
</x-app-layout>
