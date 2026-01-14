<?php

namespace App\Http\Controllers;

use App\Models\BangkomData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RiwayatController extends Controller
{
    public function index()
    {
        return view('pages.riwayat.index');
    }

    public function getHistory(Request $request)
    {
        if ($request->ajax()) {
            $nip = Auth::user()->email; // Menggunakan email sebagai NIP

            $data = BangkomData::where('nip', $nip)->whereIn('status', [1, 9]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 9) {
                        return '<span class="badge rounded-pill bg-label-primary">Verifikasi BKPSDM</span>';
                    }
                    if ($row->status == 1) {
                        return '<span class="badge rounded-pill bg-label-success">Sync</span>';
                    }
                    return $row->status;
                })
                ->addColumn('actions', function ($row) {
                    $ajukanButton = '';
                    $viewButton = '';
                    $editButton = '';
                    $deleteButton = '';

                    if ($row->status == 9) {
                        $deleteButton = '<button class="btn btn-sm btn-danger btn-hapus" data-id="' . $row->id_usulan . '"><i class="bx bx-trash"></i></button>';
                        $editButton = '<button class="btn btn-sm btn-primary btn-edit" data-id="' . $row->id_usulan . '"><i class="bx bx-edit"></i></button>';
                        $viewButton = '<button class="btn btn-sm btn-info view-detail" data-id="' . $row->id_usulan . '"><i class="bx bx-eye"></i></button>';
                    }
                    if ($row->status == 1) {
                        $ajukanButton = '';
                        $viewButton = '<button class="btn btn-sm btn-info view-detail" data-id="' . $row->id_usulan . '"><i class="bx bx-eye"></i></button>';
                    }


                    return $ajukanButton . ' ' . $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_kursus_sertipikat' => 'required',
            'namaDiklat' => 'required|string',
            'institusiPenyelenggara' => 'required|string',
            'nomorSertifikat' => 'required|string',
            'tahunDiklat' => 'required|integer|min:1900|max:2100',
            'durasiJam' => 'required|integer|min:1',
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'doc_sertifikat' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Jika validasi gagal, kirim response error dengan status 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Simpan file jika ada
        if ($request->hasFile('doc_sertifikat')) {
            $file = $request->file('doc_sertifikat');
            $fileName = time() . '_' . Str::uuid()->toString() . '.pdf';
            $filePath = $file->storeAs('sertifikat', $fileName, 'public'); // Simpan ke storage
        } else {
            $filePath = null;
        }

        // Simpan data ke database
        try {
            BangkomData::create([
                'id_usulan' => Str::uuid()->toString(),
                'jenis' => '0',
                'id_instansi' => 'A5EB03E23BA5F6A0E040640A040252AD',
                'institusi' => $request->institusiPenyelenggara,
                'id_diklat' => $request->jenisDiklat,
                'namakegiatan' => $request->namaDiklat,
                'jenis_sertifikat' => $request->jenis_kursus_sertipikat,
                'nomor_sertifikat' => $request->nomorSertifikat,
                'tahun' => $request->tahunDiklat,
                'jumlah_jp' => $request->durasiJam,
                'doc_sertifikat' => $filePath,
                'id_lokasi' => 'A5EB03E21FEAF6A0E040640A040252AD',
                'id_pns' => '000000006e5bac55016e5d0d1fc54777',
                'tanggal_mulai' => $request->tanggalMulai,
                'tanggal_selesai' => $request->tanggalSelesai,
                'created_at' => now(),
                'status' => 9,
                'nip' => Auth::user()->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data riwayat diklat berhasil disimpan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
