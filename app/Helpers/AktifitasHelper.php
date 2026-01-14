<?php

use App\Models\Aktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('catat_aktifitas')) {
    function catat_aktifitas(string $deskripsi)
    {
        $nip = Auth::check() ? Auth::user()->email : session('nip');
        
        return Aktivitas::create([
            'nip' => $nip,
            'aktivitas' => $deskripsi,
            'ip' => Request::ip(),
            'useragent' => Request::userAgent(),
        ]);
    }
}
