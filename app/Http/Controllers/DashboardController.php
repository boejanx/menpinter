<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use App\Services\SiasnService;

class DashboardController extends Controller
{

    public function index(DashboardService $dashboardService)
    {
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        $data = $dashboardService->getDashboardData();

        return view('dashboard', $data);
    }
    function getIPASN($nip)
    {
        return SiasnService::getIPASN($nip);
    }
}
