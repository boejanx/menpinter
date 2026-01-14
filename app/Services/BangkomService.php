<?php
// app/Services/BangkomService.php

namespace App\Services;

use App\Models\Bangkom;
use App\Models\BangkomData;
use App\Models\Peserta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use App\Services\ToSiasnService;

class BangkomService
{
    /**
     * Generate sertifikat dan simpan ke database
     */
    public function generateAndSaveCertificate($id)
    {
        $user       = Auth::user();
        $peserta    = Peserta::where('id_participant', $id)->first();
        $bangkom    = Bangkom::findOrFail($peserta->id_event);

        if (!$peserta || !$bangkom) {
            return [
                'success' => false,
                'message' => 'Data peserta atau bangkom tidak ditemukan.'
            ];
        }
        // Generate QR Code
        $qrData = $this->generateQRCode($id);

        // Generate PDF
        $pdf = $this->generatePDF($user, $bangkom, $qrData, $id);

        // Simpan ke database BangkomData
        $savedData = $this->saveToBangkomData($user, $bangkom, $id, $pdf);

        $updateStatus = $this->updateStatus($id);

        // Download file
        return $this->downloadCertificate($pdf, $bangkom, $user, $savedData, $id);
    }

    /**
     * Validasi peserta
     */
    private function validatePeserta($userId, $eventId)
    {
        return Peserta::where('id_event', $eventId)
            ->where('id_user', $userId)
            ->whereNotNull('presensi_at')
            ->whereHas('bangkom', function ($query) {
                $query->where('event_selesai', '<=', now());
            })
            ->first();
    }

    /**
     * Generate QR Code
     */
    private function generateQRCode($participantId)
    {
        $urlValidasi = url("/validasi/{$participantId}");

        $qrBuilder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $urlValidasi,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            logoPath: public_path('assets/img/logo/logo_kab.png'),
            logoResizeToWidth: 80,
            logoPunchoutBackground: false,
            labelText: '',
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );

        $qrResult = $qrBuilder->build();

        return [
            'base64' => $qrResult->getDataUri(),
            'url' => $urlValidasi,
            'participant_id' => $participantId
        ];
    }

    /**
     * Generate PDF sertifikat
     */
    private function generatePDF($user, $bangkom, $qrData, $participantId)
    {
        return Pdf::loadView('sertifikat.template', [
            'user' => $user,
            'bangkom' => $bangkom,
            'qr' => $qrData['base64'],
            'urlValidasi' => $qrData['url'],
            'participantId' => $participantId,
            'tanggalUnduh' => now()->format('d-m-Y H:i:s'),
        ])->setPaper('A4', 'landscape');
    }

    /**
     * Simpan data ke tabel BangkomData
     */
    private function saveToBangkomData($user, $bangkom, $peserta, $pdf)
    {
        try {
            // Simpan PDF ke storage
            $fileName = 'sertifikat_' . $peserta . '_' . $user->name . '.pdf';
            $filePath = 'sertifikat/' . $fileName;

            // Simpan file ke storage
            \Storage::disk('public')->put($filePath, $pdf->output());

            // Simpan ke BangkomData
            $bangkomData = BangkomData::create([
                'id_usulan' => $peserta,
                'jenis' => '1', // Jenis sertifikat
                'id_instansi' => 'A5EB03E23BA5F6A0E040640A040252AD', // Default
                'institusi' => $bangkom->event_lokasi ?? 'BKPSDM Kabupaten Pekalongan',
                'id_diklat' => '7',
                'namakegiatan' => $bangkom->event_tema,
                'jenis_sertifikat' => 'P',
                'nomor_sertifikat' => '800/' . ($bangkom->event_certificate ?? '0000'),
                'tahun' => $bangkom->tahun ?? date('Y'),
                'jumlah_jp' => $bangkom->event_jp,
                'doc_sertifikat' => $filePath,
                'id_lokasi' => 'A5EB03E21FEAF6A0E040640A040252AD',
                'id_pns' => $this->getIdPns($user),
                'tanggal_mulai' => $bangkom->event_mulai,
                'tanggal_selesai' => $bangkom->event_selesai,
                'created_at' => now(),
                'status' => 1,
                'nip' => $user->email,
            ]);

            dispatch(function () use ($peserta) {
                try {
                    ToSiasnService::sendFromDatabase($peserta);
                    \Log::info("SIASN sync queued for id_usulan: {$peserta}");
                } catch (\Exception $e) {
                    \Log::error("SIASN queue failed: " . $e->getMessage());
                }
            })->delay(now()->addSeconds(5)); // Delay 5 detik
            return $bangkomData;
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan ke BangkomData: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get ID PNS dari user
     */
    private function getIdPns($user)
    {
        return $user->pns_id ?? '000000006e5bac55016e5d0d1fc54777';
    }

    /**
     * Download sertifikat
     */
    private function downloadCertificate($pdf, $bangkom, $user, $bangkomData = null, $id)
    {
        $filename = "sertifikat-{$id}-{$user->name}.pdf";

        // Jika data berhasil disimpan, tambahkan info ke response
        if ($bangkomData) {
            $pdf->setOption('header-html', view('sertifikat.template', [
                'nomor_sertifikat' => $bangkomData->nomor_sertifikat,
                'id_usulan' => $bangkomData->id_usulan
            ]));
        }

        return [
            'success' => true,
            'pdf' => $pdf,
            'filename' => $filename,
            'bangkom_data' => $bangkomData,
            'nomor_sertifikat' => $bangkomData->nomor_sertifikat ?? null,
            'id_usulan' => $bangkomData->id_usulan ?? null
        ];
    }

    /**
     * Method untuk langsung download (tanpa save ke database)
     */
    public function downloadOnly($id)
    {
        $data = BangkomData::where('id_usulan', $id)->firstOrFail();

        if (!Storage::disk('public')->exists($data->doc_sertifikat)) {
            abort(404, 'File sertifikat tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $data->doc_sertifikat,
            "sertifikat-{$data->id_usulan}.pdf"
        );
    }

    /**
     * Simpan sertifikat ke BangkomData tanpa download
     */
    public function saveCertificateOnly($id)
    {
        $user = Auth::user();
        $bangkom = Bangkom::findOrFail($id);

        $peserta = $this->validatePeserta($user->id, $id);
        if (!$peserta) {
            return [
                'success' => false,
                'message' => 'Peserta tidak valid untuk sertifikat.'
            ];
        }

        $qrData = $this->generateQRCode($peserta->id_participant);
        $pdf = $this->generatePDF($user, $bangkom, $qrData, $peserta->id_participant);

        $savedData = $this->saveToBangkomData($user, $bangkom, $peserta, $pdf);

        return [
            'success' => (bool) $savedData,
            'data' => $savedData,
            'message' => $savedData ? 'Sertifikat berhasil disimpan.' : 'Gagal menyimpan sertifikat.'
        ];
    }

    function cekStatusSertifikat($id)
    {
        $peserta = Peserta::where('id_participant', $id)
            ->where('is_sync', '1')
            ->first();

        return $peserta;
    }

    function updateStatus($id)
    {
        $peserta = Peserta::where('id_participant', $id)
            ->first();

        if ($peserta) {
            $peserta->is_sync = '1';
            $peserta->save();

            return true;
        }

        return false;
    }

    public static function hapusFromSiasn($id){
        $delete = ToSiasnService::deleteFromSiasn($id);
        return $delete;
    }
}
