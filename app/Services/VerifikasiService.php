<?php
// app/Services/BangkomService.php

namespace App\Services;

use App\Models\BangkomData;
use App\Models\TtdModel;
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
use App\Services\SiasnService;

class VerifikasiService
{
    public function generateCertificate($id)
    {
        $bangkom        = BangkomData::findOrFail($id);
        $dataASN        = $this->getASN($bangkom->nip);
        $qrData         = $this->generateQRCode($id);
        $pdf            = $this->generatePDF($dataASN, $bangkom, $qrData, $id);
        $savedData      = $this->saveToBangkomData($bangkom, $pdf, $dataASN);
        
        dispatch(function () use ($id) {
                try {
                    ToSiasnService::sendFromDatabase($id);
                    \Log::info("SIASN sync queued for id_usulan: {$id}");
                } catch (\Exception $e) {
                    \Log::error("SIASN queue failed: " . $e->getMessage());
                }
            })->delay(now()->addSeconds(5));
        
        return 'success';
    }

    private function downloadCertificate($pdf, $bangkom, $bangkomData = null, $id)
    {
        $filename = "sertifikat-{$id}.pdf";

        // Jika data berhasil disimpan, tambahkan info ke response
        if ($bangkomData) {
            $pdf->setOption('header-html', view('sertifikat.template2', [
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

    private function getASN($nip)
    {
        $result = SiasnService::getASNData($nip);
        $dataASN = array(
            'id'         => $result['data']['id'],
            'nama' => $result['data']['nama'],
            'nip' => $result['data']['nipBaru'],
            'jabatan' => $result['data']['jabatanNama'],
            'unit_kerja' => $result['data']['unorNama'] . ' - ' . $result['data']['unorIndukNama'],
            'instansi' => $result['data']['instansiKerjaNama'],
        );

        return $dataASN;
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
    private function generatePDF($dataASN, $bangkom, $qrData, $id)
    {
        $ttd = TtdModel::where('status', '1')->first();
        return Pdf::loadView('sertifikat.template2', [
            'asn' => $dataASN,
            'bangkom' => $bangkom,
            'qr' => $qrData['base64'],
            'urlValidasi' => $qrData['url'],
            'participantId' => $id,
            'ttd' => $ttd,
            'tanggalUnduh' => now()->format('d-m-Y H:i:s'),
        ])->setPaper('A4', 'landscape');
    }

    /**
     * Simpan data ke tabel BangkomData
     */
    private function saveToBangkomData(BangkomData $bangkom, $pdf, $dataASN)
    {
        try {
            // Simpan PDF ke storage
            $fileName = 'sertifikat_' . $bangkom->id . '.pdf';
            $filePath = 'sertifikat/' . $fileName;

            // Simpan file ke storage
            Storage::disk('public')->put($filePath, $pdf->output());

            $bangkom->update([
                'nomor_sertifikat' => '800.2.4//2026',
                'doc_sertifikat' => $filePath,
                'id_pns' => $dataASN['id'],
                'status' => 1 // Terverifikasi
            ]);

            return $bangkom;
        } catch (\Exception $e) {
            \Log::error('Gagal simpan BangkomData', [
                'error' => $e->getMessage(),
                'bangkom_id' => $bangkom->id ?? null
            ]);
            return null;
        }
    }
}
