<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BangkomData;
use App\Models\CoachingDoc;
use App\Services\CoachingService;
use App\Http\Requests\CoachingRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CoachingController extends Controller
{
    public function __construct(
        protected CoachingService $coachingService
    ) {}

    public function index()
    {
        return view('pages.coaching.index');
    }

    // public function store(Request $request, $id = null)
    // {
    //     $isUpdate = $id !== null;

    //     $rules = [
    //         'jenis_diklat' => 'required|numeric',
    //         'nama_diklat' => 'required|string|max:255',
    //         'institusi_penyelenggara' => 'required|string|max:255',
    //         'tanggal_mulai' => 'required|date',
    //         'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    //         'dokumen_pelaksanaan' => 'nullable|file|mimes:pdf|max:900',
    //         'dokumen_evaluasi' => 'nullable|file|mimes:pdf|max:900',
    //     ];

    //     $request->validate($rules);

    //     DB::transaction(function () use ($request, $isUpdate, $id) {
    //         $data = $isUpdate ? BangkomData::findOrFail($id) : new BangkomData();

    //         if (!$isUpdate) {
    //             $data->id_usulan = Str::uuid();
    //             $data->id_pns = Auth::user()->id_pns ?? 'default';
    //             $data->jenis = 2;
    //         }

    //         $data->id_diklat        = $request->jenis_diklat;
    //         $data->namakegiatan     = $request->nama_diklat;
    //         $data->institusi        = $request->institusi_penyelenggara;
    //         $data->tanggal_mulai    = $request->tanggal_mulai;
    //         $data->tanggal_selesai  = $request->tanggal_selesai;
    //         $data->tahun            = date('Y', strtotime($request->tanggal_mulai));
    //         $data->nip              = Auth::user()->nip ?? Auth::user()->email ?? '-';
    //         $data->save();

    //         $doc = CoachingDoc::firstOrNew(['id_usulan' => $data->id_usulan]);
    //         if (!$doc->exists) {
    //             $doc->id_dokumen = Str::uuid();
    //         }

    //         $handleFileUpload = function ($fileKey, $docAttribute) use ($request, $doc) {
    //             if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
    //                 $oldPath = $doc->{$docAttribute}; 

    //                 $newPath = $request->file($fileKey)->store('bukti_dukung', 'public');
    //                 $doc->{$docAttribute} = $newPath;

    //                 if (!empty($oldPath) && Storage::disk('public')->exists($oldPath)) {
    //                     Storage::disk('public')->delete($oldPath);
    //                 }
    //             }
    //         };

    //         $handleFileUpload('dokumen_pelaksanaan', 'doc_pelaksanaan');
    //         $handleFileUpload('dokumen_evaluasi', 'doc_evaluasi');

    //         $doc->save();
    //     });

    //     return response()->json(['message' => 'Data berhasil disimpan.']);
    // }

    public function store(CoachingRequest $request, $id = null)
    {
        $user = Auth::user();

        if (!$user || !$user->email) {
            return response()->json([
                'message' => 'User tidak valid'
            ], 403);
        }

        $data = $this->coachingService->save(
            $request->validated() + $request->only([
                'dokumen_pelaksanaan',
                'dokumen_evaluasi'
            ]),
            $user,
            $id
        );

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'id_usulan' => $data->id_usulan
        ]);
    }


    public function detail($id)
    {
        try {
            $data = BangkomData::with('buktiDukung')->findOrFail($id);

            return response()->json([
                'message' => 'Data riwayat diklat berhasil diambil.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
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


    public function show(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'For AJAX only.');
        }

        return $this->coachingService->datatable();
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(403, 'For AJAX only.');
        }

        try {
            $this->coachingService->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data dan file berhasil dihapus.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.',
            ], 404);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }

    public function getDetail($id)
    {
        try {
            $data = $this->coachingService->getDetail($id);

            return response()->json($data);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function ajukan(Request $request, $id)
    {
        try {
            $this->coachingService->ajukan($id);

            return response()->json([
                'message' => 'Usulan berhasil diajukan.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
