<?php

namespace App\Http\Controllers;

use App\Models\Bangkom;
use App\Models\Peserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BangkomService;
use App\Services\ToSiasnService;

class BangkomController extends Controller
{
    protected $bangkomService;

    public function __construct(BangkomService $bangkomService)
    {
        $this->bangkomService = $bangkomService;
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 5;

        // Cache semua data (tanpa pagination)
        $allEvents = Bangkom::orderBy('created_at', 'desc')->get();

        $events = new LengthAwarePaginator(
            $allEvents->forPage($page, $perPage),
            $allEvents->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax()) {
            return view('pages.bangkom.index', compact('events'))->render();
        }

        return view('pages.bangkom.index', compact('events'));
    }

    public function detail($id)
    {
        $user = Auth::user();

        $bangkom = Bangkom::with(['peserta' => function ($q) use ($user) {
            $q->where('id_user', $user->id);
        }])->findOrFail($id);

        $peserta = $bangkom->peserta->first();
        $sudahTerdaftar = (bool) $peserta;
        $jumlahPeserta = $bangkom->peserta()->count();

        return view('pages.bangkom.detail', compact('bangkom', 'sudahTerdaftar', 'peserta', 'jumlahPeserta'));
    }

    public function presensi($id)
    {
        $bangkom = Bangkom::findOrFail($id);
        $user = Auth::user();

        $peserta = Peserta::where('id_event', $bangkom->event_id)
            ->where('id_user', $user->id)
            ->first();

        if (!$peserta) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum terdaftar di pada kegiatan ini.'
            ], 403);
        }

        if ($peserta->presensi_at) {
            return response()->json([
                'status' => 'info',
                'message' => 'Anda sudah melakukan presensi.'
            ]);
        }

        $peserta->presensi_at = now();
        $peserta->hadir = true;
        $peserta->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat.'
        ]);
    }


    public function daftar(Request $request, $id)
    {
        $user = Auth::user();
        $bangkom = Bangkom::findOrFail($id);

        // Cek jika sudah pernah daftar
        $sudah = Peserta::where('id_event', $id)
            ->where('id_user', $user->id)
            ->exists();

        if ($sudah) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Anda sudah terdaftar.'], 200);
            }
            return redirect()->route('bangkom.detail', $id)->with('info', 'Anda sudah terdaftar.');
        }

        // Simpan ke database
        Peserta::create([
            'id_participant' => Str::uuid(),
            'id_event' => $id,
            'id_user' => $user->id,
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Berhasil mendaftar.'], 200);
        }

        return redirect()->route('bangkom.detail', $id)->with('success', 'Anda berhasil mendaftar.');
    }

    public function unduhSertifikat($id)
    {
        try {
            if ($this->bangkomService->cekStatusSertifikat($id)) {
                return $this->bangkomService->downloadOnly($id);
            }

            $result = $this->bangkomService->generateAndSaveCertificate($id);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return $result['pdf']->download($result['filename']);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal mengunduh sertifikat.');
        }
    }



    public function validasi($id)
    {
        $peserta = Peserta::where('id_participant', $id)
            ->with('user', 'bangkom')
            ->first();

        // Jika data tidak ditemukan, tampilkan pesan error
        if (!$peserta) {
            return view('sertifikat.validasi', ['error' => 'Data tidak ditemukan. Pastikan ID peserta atau sertifikat yang dimasukkan benar.']);
        }

        return view('sertifikat.validasi', compact('peserta'));
    }

    public function downloadOnly($id)
    {
        $result = $this->bangkomService->downloadOnly($id);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return $result['pdf']->download($result['filename']);
    }

    /**
     * Hanya simpan ke database tanpa download
     */
    public function simpanKeBangkom($id)
    {
        $result = $this->bangkomService->saveCertificateOnly($id);

        if ($result['success']) {
            return redirect()->back()->with([
                'success' => $result['message'],
                'nomor_sertifikat' => $result['data']->nomor_sertifikat ?? null,
                'id_usulan' => $result['data']->id_usulan ?? null
            ]);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function cekStatusSertifikat($id)
    {
        $user = Auth::user();

        // Cek apakah sudah ada di BangkomData
        $bangkomData = \App\Models\BangkomData::where('id_diklat', $id)
            ->where('nip', $user->email)
            ->first();

        if ($bangkomData) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'nomor_sertifikat' => $bangkomData->nomor_sertifikat,
                    'id_usulan' => $bangkomData->id_usulan,
                    'tanggal_simpan' => $bangkomData->created_at->format('d-m-Y H:i:s'),
                    'status' => $this->getStatusText($bangkomData->status)
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    private function getStatusText($status)
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Terverifikasi',
            2 => 'Ditolak',
            9 => 'Selesai'
        ];

        return $statuses[$status] ?? 'Unknown';
    }

    function hapus_siasn($id){
        $delete = BangkomService::hapusFromSiasn($id);
        return $delete;
    }

    public function cekKirimSiasn($idUsulan)
    {
        $status = ToSiasnService::sendFromDatabase($idUsulan);
        return Response::json($status);
    }
}
