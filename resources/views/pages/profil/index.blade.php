@php
    $user = auth()->user();
@endphp

<x-app-layout>
    <div class="row">
        <div class="col-lg-12 col-sm-12 mb-4">
            <x-card bodyClass="d-flex justify-content-between flex-wrap-reverse p-0" class="bg-label-primary">
                <div class="px-4 py-4 mb-0 w-50 d-flex flex-column justify-content-between text-center text-sm-start">
                    <div class="card-title">
                        <h5 class="text-primary mb-0">Profil Pegawai</h5>
                        <p class="text-body w-sm-80 app-academy-xl-100">
                            Pastikan data profil Anda sudah lengkap dan benar. Data ini akan digunakan untuk keperluan sertifikat. Jika ada kesalahan, silakan diperbaiki.
                        </p>
                    </div>
                </div>
                <div class="w-50 d-flex justify-content-center justify-content-sm-end h-px-150 mb-sm-0 px-4">
                    <img alt="coaching" class="img-fluid" src="/assets/img/illustrations/profil.png">
                </div>
            </x-card>
        </div>

        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <img alt="User avatar" class="img-fluid rounded mb-4" height="120" src="{{ asset('assets/img/icons/pp.png') }}" width="120">
                            <div class="user-info text-center">
                                <h5 class="mb-2">{{ $user->name ?? '-' }}</h5>
                                <span class="badge bg-label-danger rounded-pill">{{ $user->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                        <div class="d-flex align-items-center me-5 gap-4">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="icon-base bx bx-timer icon-24px"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">23</h5>
                                <span>JP Tercapai</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="icon-base bx bx-clock-12 icon-24px"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">20</h5>
                                <span>JP Target</span>
                            </div>
                        </div>
                    </div>

                    <h5 class="pb-4 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                        <div id="profil-container"></div>
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="h6">Status:</span>
                                <span class="badge bg-label-success rounded-pill">Aktif</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Role:</span>
                                <span>{{ $user->roles->first()?->name ?? 'User' }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="h6">Unor: </span>
                                <span>{{ $user->unit_kerja ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <x-card>
                <form action="{{Route('profile')}}" id="form-profil" method="POST">
                    @csrf
                    @method('PATCH')
                    <x-form-input helpText="Masukkan alamat email Anda yang aktif." horizontal inputColClass="col-sm-9" labelColClass="col-sm-2" label="Email" name="email" readonly type="email"
                        value="{{ $user->email ?? '-' }}" />

                    <x-form-input helpText="Nomor Induk Pegawai Anda." horizontal inputColClass="col-sm-9" labelColClass="col-sm-2" label="NIP" name="nip" readonly type="text"
                        value="{{ $user->email ?? '-' }}" />

                    <x-form-input helpText="Nama lengkap Anda" horizontal inputColClass="col-sm-5" labelColClass="col-sm-2" label="Nama Lengkap" name="name" readonly type="text"
                        value="{{ $user->name ?? '-' }}" />

                    <x-form-input helpText="Masukkan nama unit kerja Anda." horizontal inputColClass="col-sm-9" labelColClass="col-sm-2" label="Unit Kerja" name="unit_kerja" type="text"
                        value="{{ $user->unit_kerja ?? '' }}" />

                        <x-button class="btn btn-label-primary" iconPosition="left" icon="bx bx-save" text="Simpan Perubahan" type="submit" variant="primary" />
                </form>
            </x-card>

        </div>
    </div>
</x-app-layout>
