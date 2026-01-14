<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BangkomData;
use App\Models\CoachingDoc;
use App\Services\ToSiasnService;
use App\Services\VerifikasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class VerifikasiController extends Controller
{
    protected $verifikasiService;

    public function __construct(VerifikasiService $verifikasiService)
    {
        $this->verifikasiService = $verifikasiService;
    }
    public function index()
    {
        return view('pages.verifikasi.index');
    }

    function hitung()
    {

        $stats = BangkomData::selectRaw("
        count(*) as total,
        sum(case when status = 1 then 1 else 0 end) as setuju,
        sum(case when status = 0 then 1 else 0 end) as tolak,
        sum(case when status = 9 then 1 else 0 end) as belum
    ")->first();

        $total = $stats->total;

        $data = [
            'total'  => $total,
            'setuju' => $stats->setuju,
            'tolak'  => $stats->tolak,
            'belum'  => $stats->belum,
            // Hitung Persen
            'persen_setuju' => $total > 0 ? round(($stats->setuju / $total) * 100, 1) : 0,
            'persen_tolak'  => $total > 0 ? round(($stats->tolak / $total) * 100, 1) : 0,
            'persen_belum'  => $total > 0 ? round(($stats->belum / $total) * 100, 1) : 0,
        ];

        return response()->json($data);
    }

    public function detail($id)
    {
        try {
            // Ambil data berdasarkan ID dengan relasi buktiDukung
            $data = BangkomData::with('buktiDukung')->findOrFail($id);

            // Kembalikan data dalam format JSON
            return response()->json([
                'message' => 'Data riwayat diklat berhasil diambil.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // Tangani error server
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $BangkomData = BangkomData::with('dokumen')->findOrFail($id);

        return response()->json([
            'data' => [
                'id_usulan'        => $BangkomData->id_usulan,
                'id_diklat'        => $BangkomData->id_diklat,
                'namakegiatan'     => $BangkomData->namakegiatan,
                'institusi'        => $BangkomData->institusi,
                'tanggal_mulai'    => $BangkomData->tanggal_mulai,
                'tanggal_selesai'  => $BangkomData->tanggal_selesai,
            ]
        ]);
    }


    public function data(Request $request)
    {
        if (!$request->ajax()) {
            return abort(403, 'Forbidden');
        }

        $data = BangkomData::with(['user', 'ref_bangkom'])->where('status', '=', 9);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('jenis_diklat', function ($row) {
                return e($row->ref_bangkom->jenis_diklat ?? 'Jenis Diklat Tidak Diketahui');
            })
            ->addColumn('biodata', function ($row) {
                $nama = e($row->user->name ?? 'User Tidak Ditemukan');
                $nip  = e($row->user->nip ?? '-');

                return "<strong>{$nama}</strong><br><small>NIP: {$nip}</small>";
            })
            ->addColumn('action', function ($row) {
                $buttons = [];
                $buttons[] = '<button class="btn btn-sm btn-info view-detail" data-id="' . $row->id_usulan . '"><i class="bx bx-eye"></i></button>';

                return implode(' ', $buttons);
            })
            ->rawColumns(['action', 'biodata'])
            ->make(true);
    }

    public function getDocument($id)
    {
        $BangkomData = BangkomData::with('bangkomDoc')->findOrFail($id);
        $docPelaksanaan = $BangkomData->bangkomDoc->doc_pelaksanaan ?? null;
        $docEvaluasi = $BangkomData->bangkomDoc->doc_evaluasi ?? null;

        return response()->json([
            'doc_pelaksanaan' => $docPelaksanaan ? asset('storage/' . $docPelaksanaan) : null,
            'doc_evaluasi' => $docEvaluasi ? asset('storage/' . $docEvaluasi) : null
        ]);
    }

    private function isPdf($path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mimeType === 'application/pdf';
    }

    public function destroy($id)
    {
        try {
            $BangkomData = BangkomData::where('id_usulan', $id)->firstOrFail();

            // Delete associated files if exist
            $BangkomDataDoc = CoachingDoc::where('id_usulan', $id)->first();
            if ($BangkomDataDoc) {
                if ($BangkomDataDoc->doc_pelaksanaan && Storage::disk('public')->exists($BangkomDataDoc->doc_pelaksanaan)) {
                    Storage::disk('public')->delete($BangkomDataDoc->doc_pelaksanaan);
                }
                if ($BangkomDataDoc->doc_evaluasi && Storage::disk('public')->exists($BangkomDataDoc->doc_evaluasi)) {
                    Storage::disk('public')->delete($BangkomDataDoc->doc_evaluasi);
                }
                // Optionally delete the bangkomDoc record as well
                $BangkomDataDoc->delete();
            }

            $BangkomData->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data dan file berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }

    private function saveDocument(Request $request, $idUsulan)
    {
        $doc = CoachingDoc::firstOrNew(['id_usulan' => $idUsulan]);

        // Set id_dokumen hanya jika dokumen baru (belum ada)
        if (!$doc->id_dokumen) {
            $doc->id_dokumen = Str::uuid();
        }

        // Simpan dokumen pelaksanaan
        if ($request->hasFile('dokumen_pelaksanaan')) {
            if ($doc->doc_pelaksanaan && Storage::disk('public')->exists($doc->doc_pelaksanaan)) {
                Storage::disk('public')->delete($doc->doc_pelaksanaan);
            }
            $path = $request->file('dokumen_pelaksanaan')->storeAs(
                'bukti_dukung',
                Str::uuid() . '.pdf',
                'public'
            );
            $doc->doc_pelaksanaan = $path;
        }

        // Simpan dokumen evaluasi
        if ($request->hasFile('dokumen_evaluasi')) {
            if ($doc->doc_evaluasi && Storage::disk('public')->exists($doc->doc_evaluasi)) {
                Storage::disk('public')->delete($doc->doc_evaluasi);
            }
            $path = $request->file('dokumen_evaluasi')->storeAs(
                'bukti_dukung',
                Str::uuid() . '.pdf',
                'public'
            );
            $doc->doc_evaluasi = $path;
        }

        $doc->save();
    }

    public function getDetail($id)
    {
        $usulan = BangkomData::with([
            'ref_bangkom',
            'bangkomDoc'
        ])->findOrFail($id);

        return response()->json([
            'data' => $usulan
        ]);
    }


    public function sendToSiasn(Request $request, string $idUsulan): JsonResponse
    {
        $request->validate([
            'force' => 'nullable|boolean'
        ]);

        $result = ToSiasnService::sendFromDatabase($idUsulan);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    function setuju($id)
    {
        // $result = ToSiasnService::sendFromDatabase($id);
        // if (!$result['success']) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Gagal sinkronisasi ke SIASN: ' . $result['message']
        //     ], 500);
        // }

        // $bangkom = BangkomData::findOrFail($id);
        // $bangkom->status = 1; // Setujui
        // $bangkom->save();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Usulan disetujui.'
        // ]);

        $this->verifikasiService->generateCertificate($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Usulan disetujui.'
        ]);
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500'
        ]);

        $data = BangkomData::findOrFail($id);
        $data->status = 0;
        $data->keterangan = $request->alasan;
        $data->save();

        return response()->json([
            'message' => 'Usulan berhasil ditolak'
        ]);
    }
}
