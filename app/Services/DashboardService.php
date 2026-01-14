<?php

namespace App\Services;

use App\Models\BangkomData;
use App\Models\Bangkom;
use App\Models\Peserta;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData(): array
    {
        $user = Auth::user();
        $nip = $user->email;

        $rwjp = BangkomData::getSummaryByName($nip);

        $pelatihan = Peserta::with('bangkom')
            ->where('id_user', $user->id)
            ->whereHas('bangkom', function ($query) {
                $query->where('event_selesai', '>', now());
            })
            ->latest()
            ->take(6)
            ->get();

        $upcoming = Bangkom::upcoming();

        return compact('rwjp', 'pelatihan', 'upcoming');
    }
}
