<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BangkomController;
use App\Http\Controllers\CoachingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuManagementController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\KmsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\BangkomController as AdminBangkomController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //coaching
    Route::get('/coaching', [CoachingController::class, 'index'])->name('coaching');
    Route::get('/coaching/show', [CoachingController::class, 'show'])->name('coaching.show');
    Route::post('/coaching/store', [CoachingController::class, 'store'])->name('coaching.store');
    Route::patch('/coaching/store/{id}', [CoachingController::class, 'store']);
    Route::get('/coaching/{id}/detail', [CoachingController::class, 'detail'])->name('coaching.detail');
    Route::delete('/coaching/{id}', [CoachingController::class, 'destroy']);
    Route::get('/coaching/get-detail/{id}', [CoachingController::class, 'getdetail'])->name('coaching.get-detail');
    Route::post('/coaching/ajukan/{id}', [CoachingController::class, 'ajukan'])->name('coaching.ajukan');


    //bangkom
    Route::get('/bangkom', [BangkomController::class, 'index'])->name('bangkom');
    Route::get('/bangkom/{id}', [BangkomController::class, 'detail'])->name('bangkom');
    Route::post('/presensi/{id}', [BangkomController::class, 'presensi'])->name('presensi');
    Route::post('/bangkom/{id}/presensi', [BangkomController::class, 'presensiSubmit'])->name('presensi.bangkom.submit');
    Route::post('/bangkom/{id}', [BangkomController::class, 'daftar'])->name('bangkom.daftar');
    Route::get('/bangkom/{id}/sertifikat', [BangkomController::class, 'unduhSertifikat'])->name('bangkom.sertifikat');
    Route::get('/bangkom/siasn/hapus/{id}', [BangkomController::class, 'hapus_siasn'])->name('bangkom.hapus.siasn');

    //riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/data', [RiwayatController::class, 'getHistory'])->name('riwayat.data');
    Route::post('/riwayat/simpan', [RiwayatController::class, 'store'])->name('riwayat.simpan');
    Route::post('/riwayat/sync', [SyncController::class, 'sync_siasn'])->name('riwayat.sync');

    //kms
    Route::get('/kms', [KmsController::class, 'index'])->name('kms');

    //api
    Route::get('/api/diklat', [ApiController::class, 'getDiklat']);
    Route::get('/api/struktural', [ApiController::class, 'getStruktural']);
});

Route::group(['middleware' => ['role:admin|verifikator']], function () {
    Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi');
    Route::get('/verifikasi/detail/{id}', [VerifikasiController::class, 'getDetail'])->name('verifikasi.detail');
    Route::post('/verifikasi/{id}/update', [VerifikasiController::class, 'updateVerifikasi'])->name('verifikasi.update');
    Route::get('/verifikasi/data', [VerifikasiController::class, 'data'])->name('verifikasi.getData');
    Route::get('/verifikasi/hitung', [VerifikasiController::class, 'hitung'])->name('verifikasi.hitung');
    Route::post('/verifikasi/setuju/{id}', [VerifikasiController::class, 'setuju'])->name('verifikasi.setuju');
    Route::post('/verifikasi/tolak/{id}', [VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/menu-management', [MenuManagementController::class, 'index'])->name('menu-management.index');
    Route::post('/menu-management', [MenuManagementController::class, 'store'])->name('menu-management.store');
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/manajemen-bangkom', [AdminBangkomController::class, 'index'])->name('manajemen_bangkom');
    Route::get('/manja/get', [AdminBangkomController::class, 'getData'])->name('manajemen_bangkom.getData');
    Route::post('/manja', [AdminBangkomController::class, 'store'])->name('manja.store');
    Route::get('/manja/{id}/edit', [AdminBangkomController::class, 'edit'])->name('manja.edit');
    Route::put('/manja/{id}', [AdminBangkomController::class, 'update'])->name('manja.update');
    Route::delete('/manja/{id}', [AdminBangkomController::class, 'destroy'])->name('manja.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', function () { return Socialite::driver('google')->redirect(); })->name('google.login');
    Route::get('/auth/google/callback', function () {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)), // random password
                ]
            );

            Auth::login($user); 

            return redirect('/dashboard'); 
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login: ' . $e->getMessage());
        }
    });
});

Route::get('/validasi/{id}', [BangkomController::class, 'validasi'])->name('validasi.sertifikat');
Route::get('/ipsn/{nip}', [DashboardController::class, 'getIPASN'])->name('get.ipasn');
Route::get('/cek', [SyncController::class, 'index'])->name('siasn.config');
Route::get('/siasn/send/{idUsulan}', [VerifikasiController::class, 'sendToSiasn']);
Route::get('/kms/search', [KmsController::class, 'liveSearch'])->name('kms.search');

Route::get('/cekkirim/{idUsulan}', [BangkomController::class, 'cekKirimSiasn'])->name('siasn.cekKirim');






require __DIR__ . '/auth.php';
