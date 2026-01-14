<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SiasnService;
use SiASN\Sdk\SiasnClient;
use App\Models\BangkomData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Services\SyncService;
use App\Jobs\SyncSiasnJob; // Tambahkan ini

class SyncController extends Controller
{
    public function index()
    {
        $config = SiasnService::config();
        $siasnClient = new SiasnClient($config);
        try {
            $response = $siasnClient->riwayat()->kursus('199207142020121009');

            return response()->json([
                'status' => 'success',
                'siasn_response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi SIASN: ' . $e->getMessage(),
                'config_used' => $config
            ], 500);
        }
    }

    public function sync_siasn(Request $request)
    {
        try {
            $email = Auth::user()->email;
            $userId = Auth::id();
            $force = $request->boolean('force', false);
            $fetchOnly = $request->boolean('fetch_only', false);
            
            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak tersedia'
                ], 400);
            }
            
            // Dispatch job ke queue
            $job = new SyncSiasnJob($email, $userId, $force, $fetchOnly);
            dispatch($job);
            
            return response()->json([
                'success' => true,
                'message' => 'Proses sinkronisasi telah dimulai di background',
                'job_dispatched' => true,
                'note' => 'Data akan diproses secara bertahap. Anda dapat menutup halaman ini.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gagal dispatch sync job', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai proses sinkronisasi',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Cek status job sinkronisasi
     */
    public function checkSyncStatus(Request $request)
    {
        try {
            $email = Auth::user()->email;
            $lastJobId = session('last_sync_job_id');
            
            // Anda bisa implementasi tracking job status di sini
            // Misalnya dengan Redis atau database
            
            return response()->json([
                'success' => true,
                'message' => 'Fitur check status sedang dikembangkan',
                'has_pending_job' => !empty($lastJobId)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status'
            ], 500);
        }
    }
    
    /**
     * Clear cache untuk user tertentu
     */
    public function clearSyncCache(Request $request)
    {
        $email = $request->email ?? Auth::user()->email;
        
        SyncService::clearCache($email);
        
        return response()->json([
            'success' => true,
            'message' => 'Cache sinkronisasi berhasil dihapus'
        ]);
    }
    
    /**
     * Cek data cached
     */
    public function checkCachedData(Request $request)
    {
        $email = $request->email ?? Auth::user()->email;
        $count = SyncService::getCachedKursusCount($email);
        
        return response()->json([
            'success' => true,
            'email' => $email,
            'cached_count' => $count,
            'has_cached_data' => $count > 0
        ]);
    }
}