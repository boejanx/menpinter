<?php

namespace App\Services;

use App\Models\BangkomData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SyncService
{
    /**
     * Sync kursus data ke SIASN
     */
    public static function syncKursus(string $email, bool $forceRefresh = false): array
    {
        $cacheKey = "siasn_kursus_{$email}";
        
        // Return cached data if exists and not forced refresh
        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $response = SiasnService::client()->riwayat()->kursus($email);
            
            if (!self::isValidResponse($response)) {
                throw new \Exception('Tidak ada data kursus dari SIASN atau format response tidak valid');
            }
            
            $results = self::processKursusData($response['data']);
            
            // Cache for 1 hour
            Cache::put($cacheKey, $results, 3600);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Sync kursus gagal', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty array instead of throwing to prevent breaking the flow
            return [];
        }
    }
    
    /**
     * Process and save kursus data to database
     */
    public static function syncKursusToDatabase(string $email): array
    {
        try {
            $data = self::syncKursus($email, true); // Force refresh for database sync
            $results = [
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'synced' => 0,
                'errors' => []
            ];
            
            if (empty($data)) {
                $results['errors'][] = 'Tidak ada data kursus dari SIASN';
                return $results;
            }
            
            foreach ($data as $item) {
                try {
                    $saved = self::saveKursusItem($item);
                    
                    if ($saved) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Gagal menyimpan: " . ($item['namaKursus'] ?? 'Unknown');
                    }
                    
                    $results['total']++;
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Error: " . $e->getMessage();
                    
                    Log::error('Gagal simpan kursus item', [
                        'item_id' => $item['id'] ?? 'unknown',
                        'nama' => $item['namaKursus'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            $results['synced'] = $results['success'];
            Log::info('Sync kursus ke database selesai', $results);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Sync kursus ke database gagal', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return [
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'synced' => 0,
                'errors' => ['Sync gagal: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Save single kursus item to database
     */
    protected static function saveKursusItem(array $item): bool
    {
        try {
            // Parse dates
            $tanggalMulai = self::parseDate($item['tanggalKursus'] ?? null);
            $tanggalSelesai = self::parseDate($item['tanggalSelesaiKursus'] ?? null);
            
            // Jika tanggal selesai tidak ada, gunakan tanggal mulai
            if (!$tanggalSelesai && $tanggalMulai) {
                $tanggalSelesai = $tanggalMulai;
            }
            
            // Extract document path
            $docPath = self::extractDocumentPath($item['path'] ?? null);
            
            // Tentukan tahun dari tanggal mulai atau tahunKursus
            $tahun = self::determineYear($tanggalMulai, $item['tahunKursus'] ?? null);
            
            BangkomData::updateOrCreate(
                [
                    'id_siasn' => $item['id'] ?? null,
                    'nip' => $item['nipBaru'] ?? null,
                ],
                [
                    'id_usulan'         => Str::uuid(),
                    'id_siasn'          => $item['id'] ?? null,
                    'nip'               => $item['nipBaru'] ?? null,
                    'id_pns'            => $item['idPns'] ?? null,
                    'namakegiatan'      => $item['namaKursus'] ?? null,
                    'institusi'         => $item['institusiPenyelenggara'] ?? null,
                    'id_diklat'         => $item['jenisDiklatId'] ?? null,
                    'jenis'             => "0", 
                    'jenis_kursus'      => $item['jenisKursusId'] ?? null,
                    'jenis_kursus_nama' => $item['jenisKursusNama'] ?? null,
                    'jenis_sertifikat'  => $item['jenisKursusSertifikat'] ?? null,
                    'jumlah_jp'         => self::parseJumlahJam($item['jumlahJam'] ?? 0),
                    'nomor_sertifikat'  => $item['noSertipikat'] ?? null,
                    'tahun'             => $tahun,
                    'tanggal_mulai'     => $tanggalMulai,
                    'tanggal_selesai'   => $tanggalSelesai,
                    'doc_sertifikat'    => $docPath,
                    'status'            => 1, // Status aktif/synced
                    'created_at'        => self::parseDate($item['createdAt'] ?? null) ?? now(),
                    'updated_at'        => self::parseDate($item['updatedAt'] ?? null) ?? now(),
                ]
            );
            
            Log::info('Berhasil simpan/update kursus dari SIASN', [
                'id' => $item['id'],
                'nama' => $item['namaKursus'],
                'nip' => $item['nipBaru']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Gagal save kursus item dari SIASN', [
                'item_id' => $item['id'] ?? 'unknown',
                'nama' => $item['namaKursus'] ?? 'unknown',
                'nip' => $item['nipBaru'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Parse date from SIASN format (d-m-Y) to Y-m-d
     */
    protected static function parseDate(?string $date): ?string
    {
        if (empty($date) || $date === 'null' || strtolower($date) === 'null') {
            return null;
        }
        
        try {
            // Try d-m-Y format (common in SIASN)
            if (str_contains($date, '-')) {
                $parts = explode('-', $date);
                if (count($parts) === 3 && strlen($parts[2]) === 4) {
                    return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                }
            }
            
            // Try Y-m-d format
            return Carbon::parse($date)->format('Y-m-d');
            
        } catch (\Exception $e) {
            Log::warning('Gagal parse date dari SIASN', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Extract document path from SIASN response
     */
    protected static function extractDocumentPath($pathData): ?string
    {
        if (empty($pathData)) {
            return null;
        }
        
        // Jika sudah string langsung return
        if (is_string($pathData)) {
            return $pathData;
        }
        
        // Jika array, cari dok_uri
        if (is_array($pathData)) {
            // Flatten array jika nested
            $flatArray = [];
            array_walk_recursive($pathData, function($value, $key) use (&$flatArray) {
                $flatArray[$key] = $value;
            });
            
            // Cari dok_uri
            if (isset($flatArray['dok_uri'])) {
                return $flatArray['dok_uri'];
            }
            
            // Cek tiap elemen
            foreach ($pathData as $item) {
                if (is_array($item) && isset($item['dok_uri'])) {
                    return $item['dok_uri'];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Parse jumlah jam to integer
     */
    protected static function parseJumlahJam($jumlahJam): int
    {
        if (is_numeric($jumlahJam)) {
            return (int) $jumlahJam;
        }
        
        if (is_string($jumlahJam)) {
            // Remove non-numeric characters except decimal point
            $cleaned = preg_replace('/[^0-9.]/', '', $jumlahJam);
            return (int) floatval($cleaned);
        }
        
        return 0;
    }
    
    /**
     * Validate SIASN response
     */
    protected static function isValidResponse($response): bool
    {
        if (!isset($response['success']) || $response['success'] !== true) {
            return false;
        }
        
        if (!isset($response['data']) || !is_array($response['data'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Process raw kursus data
     */
    protected static function processKursusData(array $data): array
    {
        $processed = [];
        
        foreach ($data as $item) {
            // Skip invalid items
            if (empty($item['id']) || empty($item['namaKursus'])) {
                continue;
            }
            
            $processed[] = [
                'id' => $item['id'] ?? null,
                'nipBaru' => $item['nipBaru'] ?? null,
                'idPns' => $item['idPns'] ?? null,
                'namaKursus' => $item['namaKursus'] ?? null,
                'institusiPenyelenggara' => $item['institusiPenyelenggara'] ?? null,
                'jenisKursusId' => $item['jenisKursusId'] ?? null,
                'jenisKursusNama' => $item['jenisKursusNama'] ?? null,
                'jenisKursusSertifikat' => $item['jenisKursusSertifikat'] ?? null,
                'jenisDiklatId' => $item['jenisDiklatId'] ?? null,
                'jumlahJam' => $item['jumlahJam'] ?? 0,
                'noSertipikat' => $item['noSertipikat'] ?? null,
                'tahunKursus' => $item['tahunKursus'] ?? null,
                'tanggalKursus' => $item['tanggalKursus'] ?? null,
                'tanggalSelesaiKursus' => $item['tanggalSelesaiKursus'] ?? null,
                'path' => $item['path'] ?? null,
                'createdAt' => $item['createdAt'] ?? null,
                'updatedAt' => $item['updatedAt'] ?? null,
            ];
        }
        
        return $processed;
    }
    
    /**
     * Determine year from date or tahunKursus
     */
    protected static function determineYear(?string $tanggalMulai, ?string $tahunKursus): ?string
    {
        if ($tanggalMulai) {
            try {
                return Carbon::parse($tanggalMulai)->format('Y');
            } catch (\Exception $e) {
                // Continue to other methods
            }
        }
        
        if ($tahunKursus && is_numeric($tahunKursus)) {
            return $tahunKursus;
        }
        
        return date('Y');
    }
    
    /**
     * Map jenis kursus sertifikat ke jenis diklat
     */
    protected static function getJenisKursusMapping(string $jenisSertifikat): array
    {
        $mapping = [
            'SEMINAR/WORKSHOP/SEJENISNYA' => [
                'jenis' => 2, // Contoh: jenis 2 untuk seminar/workshop
                'id_diklat' => '9' // Contoh: id_diklat 9 sesuai dengan response
            ],
            'DIKLAT TEKNIS' => [
                'jenis' => 1,
                'id_diklat' => '1'
            ],
            'DIKLAT KEPEMIMPINAN' => [
                'jenis' => 3,
                'id_diklat' => '3'
            ],
            // Tambahkan mapping lainnya sesuai kebutuhan
        ];
        
        // Default mapping jika tidak ditemukan
        $default = [
            'jenis' => 2,
            'id_diklat' => '9'
        ];
        
        return $mapping[$jenisSertifikat] ?? $default;
    }
    
    /**
     * Clear sync cache for specific email
     */
    public static function clearCache(string $email): void
    {
        Cache::forget("siasn_kursus_{$email}");
    }
    
    /**
     * Get kursus data from cache if exists
     */
    public static function getCachedKursus(string $email): ?array
    {
        return Cache::get("siasn_kursus_{$email}");
    }
    
    /**
     * Get count of cached kursus
     */
    public static function getCachedKursusCount(string $email): int
    {
        $data = self::getCachedKursus($email);
        return $data ? count($data) : 0;
    }
    
    /**
     * Check if user has kursus data in SIASN
     */
    public static function hasKursusData(string $email): bool
    {
        $data = self::getCachedKursus($email);
        return !empty($data);
    }
    
    /**
     * Get latest sync status
     */
    public static function getSyncStatus(string $email): array
    {
        $cacheKey = "siasn_kursus_{$email}";
        $hasCache = Cache::has($cacheKey);
        
        return [
            'has_cache' => $hasCache,
            'cache_expires_at' => $hasCache ? Cache::get($cacheKey . '_expiry') : null,
            'cached_count' => $hasCache ? count(Cache::get($cacheKey)) : 0,
            'last_sync' => Cache::get($cacheKey . '_last_sync')
        ];
    }
}