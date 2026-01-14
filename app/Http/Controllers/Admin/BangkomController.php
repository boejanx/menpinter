<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bangkom as BangkomSch;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class BangkomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        // Statistik untuk card di bagian atas (opsional, bisa Anda kembangkan)
        $stats = [
            'total_event' => BangkomSch::count(),
            'event_akan_datang' => BangkomSch::where('event_mulai', '>', now())->count(),
            'event_selesai' => BangkomSch::where('event_selesai', '<', now())->count(),
            'event_berlangsung' => BangkomSch::where('event_mulai', '<=', now())->where('event_selesai', '>=', now())->count(),
        ];

        return view('pages.manajemen.index', compact('stats'));
    }

    function getData(Request $request)
    {
        if ($request->ajax()) {
            // Menggunakan withCount untuk efisiensi query, menghitung jumlah relasi 'participants'
            $data = BangkomSch::withCount('peserta')->latest();

            return DataTables::of($data)
                ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
                ->addColumn('waktu_pelaksanaan', function ($row) {
                    // Format tanggal mulai dan selesai
                    $mulai = Carbon::parse($row->event_mulai)->isoFormat('D MMMM YYYY');
                    $selesai = Carbon::parse($row->event_selesai)->isoFormat('D MMMM YYYY');
                    return $mulai . ' s/d ' . $selesai;
                })
                ->addColumn('status', function ($row) {
                    // Logika untuk menentukan status acara
                    $now = Carbon::now();
                    $mulai = Carbon::parse($row->event_mulai);
                    $selesai = Carbon::parse($row->event_selesai);

                    if ($now->lt($mulai)) {
                        return '<span class="badge bg-label-info">Akan Datang</span>';
                    } elseif ($now->between($mulai, $selesai)) {
                        return '<span class="badge bg-label-success">Berlangsung</span>';
                    } else {
                        return '<span class="badge bg-label-primary">Selesai</span>';
                    }
                })
                ->editColumn('participants_count', function ($row) {
                    // Menambahkan teks 'orang' di belakang jumlah peserta
                    return $row->participants_count . ' orang';
                })
                ->addColumn('action', function ($row) {
                    // Menambahkan tombol aksi (lihat, edit, hapus)
                    $btn = '<div class="d-flex">';
                    $btn .= '<button data-id="' . $row->event_id . '" class="btn-info btn btn-sm item-edit"><i class="bx bxs-eye"></i></button> ';
                    $btn .= '<button data-id="' . $row->event_id . '" class="btn-warning btn btn-sm edit-btn"><i class="bx bxs-edit"></i></button> ';
                    $btn .= '<button data-id="' . $row->event_id . '" class="btn-danger btn btn-sm btn-delete"><i class="bx bxs-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status']) // Kolom yang mengandung HTML
                ->make(true);
        } else {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_tema' => 'required|string|max:255',
            'event_mulai' => 'required|date',
            'event_selesai' => 'required|date|after_or_equal:event_mulai',
            'event_lokasi' => 'nullable|string|max:255',
            'event_link' => 'nullable|url|max:255',
            'event_jp' => 'nullable|integer|min:0',
            'event_keterangan' => 'nullable|string',
            'event_flyer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil data tervalidasi
        $data = $validator->validated();

        // Bersihkan keterangan (gunakan helper clean)
        if (isset($data['event_keterangan'])) {
            $data['event_keterangan'] = clean($data['event_keterangan']);
        }

        // Handle upload file flyer
        if ($request->hasFile('event_flyer')) {
            $path = $request->file('event_flyer')->store('flyers', 'public');
            $data['event_flyer'] = Storage::url($path); // -> "/storage/flyers/namafile.jpg"
        }

        // Tambahkan UUID jika kamu memang ingin pakai UUID sebagai ID
        // Tapi pastikan field `event_id` di tabel TIDAK auto-increment
        $data['event_id'] = Str::uuid();

        // Simpan ke DB
        BangkomSch::create($data);

        return response()->json(['success' => 'Kegiatan baru berhasil ditambahkan.']);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = BangkomSch::find($id);
        if (!$event) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        $event->event_mulai = Carbon::parse($event->event_mulai)->format('Y-m-d H:i');
        $event->event_selesai = Carbon::parse($event->event_selesai)->format('Y-m-d H:i');
        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'event_tema' => 'required|string|max:255',
            // ... validasi lain ...
            'event_flyer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $event = BangkomSch::find($id);
        if (!$event) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $data = $request->except('event_flyer');

        // Handle file upload jika ada file baru
        if ($request->hasFile('event_flyer')) {
            // 1. Hapus file lama jika ada
            if ($event->event_flyer) {
                // Konversi URL kembali ke path storage
                $oldPath = str_replace('/storage', 'public', $event->event_flyer);
                Storage::delete($oldPath);
            }
            // 2. Simpan file baru
            $path = $request->file('event_flyer')->store('public/flyers');
            $data['event_flyer'] = Storage::url($path);
        }

        $event->update($data);

        return response()->json(['success' => 'Kegiatan berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $event = BangkomSch::find($id);
        if ($event) {
            // Hapus file gambar terkait sebelum menghapus data event
            if ($event->event_flyer) {
                $oldPath = str_replace('/storage', 'public', $event->event_flyer);
                Storage::delete($oldPath);
            }
            $event->delete();
            return response()->json(['success' => 'Data berhasil dihapus.']);
        }
        return response()->json(['error' => 'Data tidak ditemukan.'], 404);
    }
}
