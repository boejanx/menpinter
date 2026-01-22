<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\SiasnService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        return view('dashboard', $data);
    }

    public function getIPASN(string $nip): JsonResponse
    {
        if (!preg_match('/^\d{8,20}$/', $nip)) {
            return response()->json([
                'success' => false,
                'message' => 'Format NIP tidak valid.'
            ], 422);
        }

        try {
            //$data = SiasnService::getASN($nip);
            $data = SiasnService::jfu();
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data IP ASN.',
            ], 502);
        }
    }
}
