<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Aktivitas;
use Illuminate\Support\Facades\Request;

class Aktifitas
{
    public static function catat($deskripsi)
    {
      $nip = Auth::check() ? Auth::user()->email : session('nip');
        return Aktivitas::create([
            'nip' => $nip,
            'aktivitas' => $deskripsi,
            'ip'=>Request::ip(),
            'useragent'=>Request::userAgent(),
        ]);
    }
}