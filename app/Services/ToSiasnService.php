<?php

namespace App\Services;

use App\Models\BangkomData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ToSiasnService
{
    /**
     * Proses lengkap: ambil dari DB dan kirim ke SIASN
     * Hanya mengirim SATU data per ID usulan
     *
     * @param string|int $idUsulan
     * @param array|null $files
     * @return array
     */
    public static function sendFromDatabase($idUsulan): array
    {
        try {
            Log::info('Memulai proses pengiriman ke SIASN', ['id_usulan' => $idUsulan]);

            $data = self::getDataFromDatabase($idUsulan);
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk ID usulan: ' . $idUsulan
                ];
            }

            $mappedData = self::mapToSiasnFormat($data);
            self::validateSiasnData($mappedData);

            $files = null;

            if (!empty($data[0]['doc_sertifikat'])) {
                if (!Storage::disk('public')->exists($data[0]['doc_sertifikat'])) {
                    throw new \Exception('File sertifikat tidak ditemukan');
                }

                $realPath = Storage::disk('public')->path($data[0]['doc_sertifikat']);

                if (filesize($realPath) > 2 * 1024 * 1024) {
                    throw new \Exception('Ukuran file sertifikat terlalu besar (>2MB)');
                }

                $files = $realPath;
            }

            $result = self::sendSingleToSiasnApi($mappedData, $files);

            self::updateDatabaseStatus(
                $idUsulan,
                $result['success'] ? 'TERKIRIM' : 'GAGAL',
                $result
            );

            return $result;
        } catch (\Exception $e) {
            Log::error('Gagal memproses data dari database ke SIASN', [
                'id_usulan' => $idUsulan,
                'error' => $e->getMessage(),
            ]);

            self::updateDatabaseStatus($idUsulan, 'ERROR', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memproses data: ' . $e->getMessage(),
            ];
        }
    }


    /**
     * Ambil data dari database berdasarkan ID usulan
     * Hanya ambil SATU data (yang pertama)
     *
     * @param string|int $idUsulan
     * @return array
     */
    protected static function getDataFromDatabase($idUsulan): array
    {
        try {
            $data = BangkomData::where('id_usulan', $idUsulan)
                ->first();

            if (!$data) {
                // Cek apakah data sudah terkirim
                $alreadySent = BangkomData::where('id_usulan', $idUsulan)
                    ->where('status', '1')
                    ->exists();

                if ($alreadySent) {
                    Log::warning('Data sudah pernah dikirim ke SIASN', ['id_usulan' => $idUsulan]);
                    return [];
                }

                Log::warning('Data tidak ditemukan di database', ['id_usulan' => $idUsulan]);
                return [];
            }

            // Return sebagai array dengan satu item
            return [$data->toArray()];
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data dari database', [
                'id_usulan' => $idUsulan,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Mapping data dari format database ke format SIASN
     * Hanya mapping SATU data
     *
     * @param array $data
     * @return array
     */
    protected static function mapToSiasnFormat(array $data): array
    {
        $mappedData = [];

        // Hanya proses data pertama
        if (empty($data)) {
            return [];
        }

        $item = $data[0];

        try {
            // Validasi field required
            $requiredFields = ['tanggal_mulai', 'tanggal_selesai', 'id_pns', 'namakegiatan'];
            foreach ($requiredFields as $field) {
                if (empty($item[$field])) {
                    throw new \Exception("Field $field kosong pada data dengan ID: " . ($item['id'] ?? 'unknown'));
                }
            }

            // Format tanggal
            $tanggalMulai = Carbon::createFromFormat('Y-m-d', $item['tanggal_mulai'])->format('d-m-Y');
            $tanggalSelesai = Carbon::createFromFormat('Y-m-d', $item['tanggal_selesai'])->format('d-m-Y');

            $mappedItem = [
                "instansiId"             => $item['id_instansi'] ?? null,
                "institusiPenyelenggara" => $item['institusi'] ?? null,
                "jenisDiklatId"          => $item['id_diklat'] ?? 0,
                "jenisKursus"            => $item['jenis_kursus'] ?? null,
                "jenisKursusSertipikat"  => $item['jenis_sertifikat'] ?? null,
                "jumlahJam"              => (int) ($item['jumlah_jp'] ?? 0),
                "lokasiId"               => $item['id_lokasi'] ?? null,
                "namaKursus"             => $item['namakegiatan'] ?? null,
                "nomorSertipikat"        => $item['nomor_sertifikat'] ?? null,
                "pnsOrangId"             => $item['id_pns'] ?? null,
                "tahunKursus"            => (int) ($item['tahun'] ?? date('Y')),
                "tanggalKursus"          => $tanggalMulai,
                "tanggalSelesaiKursus"   => $tanggalSelesai
            ];

            // Hapus nilai null
            $mappedItem = array_filter($mappedItem, function ($value) {
                return $value !== null;
            });

            // Kembalikan SATU item dalam array
            return [$mappedItem];
        } catch (\Exception $e) {
            Log::error('Gagal mapping data item', [
                'item_id' => $item['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Gagal mapping data: " . $e->getMessage());
        }
    }

    /**
     * Validasi data sebelum dikirim ke SIASN
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected static function validateSiasnData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception('Tidak ada data yang valid untuk dikirim');
        }

        // Hanya validasi data pertama
        $item = $data[0];
        $requiredFields = ['pnsOrangId', 'namaKursus', 'tanggalKursus', 'tanggalSelesaiKursus'];

        foreach ($requiredFields as $field) {
            if (empty($item[$field])) {
                throw new \Exception("Field $field wajib diisi");
            }
        }

        // Validasi format tanggal
        if (!self::isValidDate($item['tanggalKursus'], 'd-m-Y')) {
            throw new \Exception("Format tanggalKursus tidak valid");
        }

        if (!self::isValidDate($item['tanggalSelesaiKursus'], 'd-m-Y')) {
            throw new \Exception("Format tanggalSelesaiKursus tidak valid");
        }
    }

    /**
     * Kirim SATU DATA ke API SIASN
     *
     * @param array $mappedData
     * @param array|null $files
     * @return array
     */
    protected static function sendSingleToSiasnApi(array $mappedData, $files = null): array
    {
        try {
            $client = SiasnService::client();

            $singleData = $mappedData[0];


            // Debug: log data yang akan dikirim
            Log::debug('Data untuk SIASN:', $singleData);
            Log::debug('File untuk SIASN:',  $files);

            // Kirim ke SIASN - SATU DATA SAJA, bukan array
            if ($files) {
                $response = $client->kursus()
                    ->create($singleData)  // ← SINGLE OBJECT, bukan array
                    ->includeDokumen($files)
                    ->save();
            } else {
                $response = $client->kursus()
                    ->create($singleData)
                    ->save();
            }

            // Parse response dari SIASN
            return self::parseSiasnResponse($response);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim ke API SIASN', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Gagal mengirim ke API SIASN: ' . $e->getMessage());
        }
    }

    /**
     * Parse response dari SIASN
     * Handle berbagai format response
     *
     * @param mixed $response
     * @return array
     */
    protected static function parseSiasnResponse($response): array
    {
        try {
            // CASE 1: Response adalah array (custom wrapper)
            if (is_array($response)) {
                Log::debug('Response dari SIASN adalah array', [
                    'keys' => array_keys($response)
                ]);

                $success = $response['success'] ?? false;
                $statusCode = $response['status_code'] ?? $response['code'] ?? 200;
                $message = $response['message'] ?? $response['msg'] ?? ($success ? 'Success' : 'Error');
                $data = $response['data'] ?? $response['response'] ?? $response;

                return [
                    'success' => (bool) $success,
                    'status_code' => (int) $statusCode,
                    'data' => $data,
                    'message' => $message,
                    'timestamp' => Carbon::now()->toISOString()
                ];
            }

            // CASE 2: Response adalah object dengan metode getStatusCode()
            if (is_object($response) && method_exists($response, 'getStatusCode')) {
                $statusCode = $response->getStatusCode();
                $success = $statusCode >= 200 && $statusCode < 300;

                // Coba dapatkan body content
                if (method_exists($response, 'getBody')) {
                    $body = $response->getBody()->getContents();
                    $data = json_decode($body, true) ?? [];
                    $message = $data['message'] ?? $data['msg'] ?? ($success ? 'Success' : 'Error ' . $statusCode);
                } else {
                    $data = [];
                    $message = $success ? 'Success' : 'Error ' . $statusCode;
                }

                return [
                    'success' => $success,
                    'status_code' => $statusCode,
                    'data' => $data,
                    'message' => $message,
                    'timestamp' => Carbon::now()->toISOString()
                ];
            }

            // CASE 3: Unknown response format
            Log::warning('Format response tidak dikenali', [
                'type' => gettype($response),
                'value' => $response
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'data' => $response,
                'message' => 'Format response tidak dikenali dari SIASN',
                'timestamp' => Carbon::now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Gagal parse response dari SIASN', [
                'error' => $e->getMessage(),
                'response_type' => gettype($response)
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'data' => $response,
                'message' => 'Gagal parse response: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ];
        }
    }


    /**
     * Mapping single item
     */
    protected static function mapSingleItemToSiasnFormat(array $item): array
    {
        // Format tanggal
        $tanggalMulai = Carbon::createFromFormat('Y-m-d', $item['tanggal_mulai'])->format('d-m-Y');
        $tanggalSelesai = Carbon::createFromFormat('Y-m-d', $item['tanggal_selesai'])->format('d-m-Y');

        return [
            "instansiId"             => $item['id_instansi'] ?? null,
            "institusiPenyelenggara" => $item['institusi'] ?? null,
            "jenisDiklatId"          => (int) ($item['id_diklat'] ?? 0),
            "jenisKursus"            => $item['jenis_kursus'] ?? null,
            "jenisKursusSertipikat"  => $item['jenis_sertifikat'] ?? null,
            "jumlahJam"              => (int) ($item['jumlah_jp'] ?? 0),
            "lokasiId"               => $item['id_lokasi'] ?? null,
            "namaKursus"             => $item['namakegiatan'] ?? null,
            "nomorSertipikat"        => $item['nomor_sertifikat'] ?? null,
            "pnsOrangId"             => $item['id_pns'] ?? null,
            "tahunKursus"            => (int) ($item['tahun'] ?? date('Y')),
            "tanggalKursus"          => $tanggalMulai,
            "tanggalSelesaiKursus"   => $tanggalSelesai
        ];
    }

    /**
     * Cek status pengiriman
     *
     * @param string|int $idUsulan
     * @return array
     */
    public static function checkStatus($idUsulan): array
    {
        try {
            $data = BangkomData::where('id_usulan', $idUsulan)
                ->select('status_siasn', 'tanggal_kirim_siasn', 'catatan_siasn', 'siasn_status_code')
                ->first();

            if (!$data) {
                return [
                    'found' => false,
                    'message' => 'Data tidak ditemukan'
                ];
            }

            return [
                'found' => true,
                'status_siasn' => $data->status_siasn,
                'tanggal_kirim_siasn' => $data->tanggal_kirim_siasn,
                'catatan_siasn' => $data->catatan_siasn,
                'siasn_status_code' => $data->siasn_status_code,
                'is_sent' => $data->status_siasn === 'TERKIRIM'
            ];
        } catch (\Exception $e) {
            Log::error('Gagal cek status', [
                'id_usulan' => $idUsulan,
                'error' => $e->getMessage()
            ]);

            return [
                'found' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validasi format tanggal
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    private static function isValidDate(string $date, string $format = 'd-m-Y'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Update status di database
     */
    protected static function updateDatabaseStatus($idUsulan, string $status, array $response = []): bool
    {
        try {
            $updateData = [
                'status_siasn' => $status,
                'tanggal_kirim_siasn' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            if (isset($response['message'])) {
                $updateData['catatan_siasn'] = $response['message'];
            }

            if (isset($response['status_code'])) {
                $updateData['siasn_status_code'] = $response['status_code'];
            }

            if (isset($response['data'])) {
                $updateData['siasn_response'] = json_encode($response['data']);
            }

            $updated = BangkomData::where('id_usulan', $idUsulan)
                ->update($updateData);

            return $updated > 0;
        } catch (\Exception $e) {
            Log::error('Gagal update status di database', [
                'id_usulan' => $idUsulan,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public static function deleteFromSiasn($idSiasn): array
    {
        try {

            $client = SiasnService::client();

            $client->kursus()->remove($idSiasn);

            return [
                'success' => true,
                'message' => 'Data berhasil dihapus dari SIASN'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
