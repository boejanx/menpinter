<x-app-layout>
    <div class="row g-6 mb-6">
        <div class="col-md-12">
            <div class="card p-0 mb-0">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0">
                    <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-6 py-6">
                        <span class="card-title mb-2 px-md-12 h4">
                            KNOWLEDGE MANAGEMENT SYSTEM<br>
                        </span>
                        <p class="mb-4">Temukan Pengetahuan Baru untuk meningkatkan Profesionalisme ASN Pemerintah Kabupaten Pekalongan</p>
                        <div class="d-flex align-items-center justify-content-between col-md-6 col-xs-12 col-sm-12 mx-auto">
                            <input class="form-control" id="search-kms" placeholder="Cari Judul ..." type="search">
                        </div>
                        <div class="mt-4 text-center">
                            <button class="btn btn-primary"> <i class="fas fa-plus"></i> &nbsp; Tambahkan Pengetahuan </button> &nbsp;
                            <button class="btn btn-success"> <i class="fas fa-file"></i> &nbsp; Dokumenku </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7 col-lg-3 order-2 mb-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Kategori</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex align-items-center mb-4 kms-category {{ empty($categoryId) ? 'active' : '' }}" onclick="window.location='{{ route('kms') }}'" style="cursor:pointer">

                            <div class="avatar flex-shrink-0 me-3 h3">
                                    <i class="fas fa-code-merge"></i>
                                </div>
                            <h6 class="mb-0">Semua Kategori</h6>
                        </li>

                        {{-- Loop through categories --}}
                        @foreach ($categories as $category)
                            <li class="d-flex align-items-center kms-category {{ ($categoryId ?? null) == $category->cat_id ? 'active' : '' }}"
                                onclick="window.location='?category={{ $category->cat_id }}'" style="cursor:pointer">

                                <div class="avatar flex-shrink-0 me-3 h4">
                                    <i class="fas fa-code-merge"></i>
                                </div>

                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <small class="d-block">{{ $category->getCategory() }}</small>
                                        <h6 class="fw-normal mb-0">{{ $category->keterangan }}</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-2">
                                        <h6 class="fw-normal mb-0">{{ $category->jumlah }}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-lg-9 order-1 mb-6">
            <div class="card h-100">
                <div class="card">
                    @if ($content->isEmpty())
                        <div class="alert alert-warning text-center m-4">
                            <i class="bx bx-info-circle me-1"></i>
                            Belum ada Dokumen yang tersedia
                        </div>
                    @else
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm text-nowrap table-border-top-0 table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="80%">Judul</th>
                                    <th class="text-center" width="20%">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="kms-body">
                                @foreach ($content as $c)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0">{{ $c->judul }}</h6>
                                                    <small class="text-body">
                                                        <div class="badge bg-label-primary badge-info">{{ $c->category->cat_name }}</div>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn rounded-pill btn-dark"> <i class="fas fa-list"> </i> &nbsp Detail</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-body mt-2">
                        <div class="table-pagination row mx-3 justify-content-between">
                            {{ $content->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
