<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SyncService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SyncSiasnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 menit timeout
    public $tries = 3; // 3 kali percobaan
    public $backoff = [60, 120]; // Tunggu 1 dan 2 menit untuk retry
    
    protected $email;
    protected $userId;
    protected $force;
    protected $fetchOnly;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $userId, $force = false, $fetchOnly = false)
    {
        $this->email = $email;
        $this->userId = $userId;
        $this->force = $force;
        $this->fetchOnly = $fetchOnly;
        $this->jobId = uniqid('sync_', true);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Memulai proses sinkronisasi SIASN', [
            'job_id' => $this->jobId,
            'user_id' => $this->userId,
            'email' => $this->email
        ]);

        try {
            if ($this->fetchOnly) {
                // Hanya ambil data
                $data = SyncService::syncKursus($this->email, $this->force);
                
                Log::info('Sinkronisasi fetch-only selesai', [
                    'job_id' => $this->jobId,
                    'total_data' => count($data)
                ]);
            } else {
                // Ambil dan simpan ke database
                $results = SyncService::syncKursusToDatabase($this->email);
                
                Log::info('Sinkronisasi database selesai', [
                    'job_id' => $this->jobId,
                    'total' => $results['total'],
                    'success' => $results['success'],
                    'failed' => $results['failed']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Gagal menjalankan sinkronisasi SIASN', [
                'job_id' => $this->jobId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Biarkan queue handle retry
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job sinkronisasi SIASN gagal', [
            'job_id' => $this->jobId,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}