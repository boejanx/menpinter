<?php

namespace App\Services;

use App\Models\BangkomData;
use App\Models\CoachingDoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CoachingService
{
    public function save(array $payload, $user, $id = null): BangkomData
    {
        return DB::transaction(function () use ($payload, $user, $id) {

            $isUpdate = $id !== null;

            $data = $isUpdate
                ? BangkomData::where('id_usulan', $id)->lockForUpdate()->firstOrFail()
                : new BangkomData();

            if (!$isUpdate) {
                $data->id_usulan = Str::uuid();
                $data->id_pns = $user->id_pns;
                 // Draft
            }
            $data->jenis            = $payload['jenis'] ?? 2;
            $data->id_diklat        = $payload['jenis_diklat'];
            $data->namakegiatan     = $payload['nama_diklat'];
            $data->institusi        = $payload['institusi_penyelenggara'];
            $data->tanggal_mulai    = $payload['tanggal_mulai'];
            $data->tanggal_selesai  = $payload['tanggal_selesai'];
            $data->tahun            = date('Y', strtotime($payload['tanggal_mulai']));
            $data->nip              = $user->nip ?? $user->email ?? '-';
            $data->jenis_sertifikat = 'T';
            $data->jumlah_jp        = '2';
            $data->id_instansi      = 'A5EB03E23BA5F6A0E040640A040252AD';
            $data->id_lokasi        = 'A5EB03E23BA5F6A0E040640A040252AD';
            $data->status = 8;
            $data->save();

            $doc = CoachingDoc::firstOrNew([
                'id_usulan' => $data->id_usulan
            ]);

            if (!$doc->exists) {
                $doc->id_dokumen = Str::uuid();
            }

            $deletedFiles = [];

            $this->handleUpload($payload, $doc, 'dokumen_pelaksanaan', 'doc_pelaksanaan', $deletedFiles);
            $this->handleUpload($payload, $doc, 'dokumen_evaluasi', 'doc_evaluasi', $deletedFiles);

            $doc->save();

            DB::afterCommit(function () use ($deletedFiles) {
                foreach ($deletedFiles as $file) {
                    Storage::disk('public')->delete($file);
                }
            });

            return $data;
        });
    }

    public function delete(string $idUsulan): void
    {
        DB::transaction(function () use ($idUsulan) {

            $bangkom = BangkomData::where('id_usulan', $idUsulan)
                ->lockForUpdate()
                ->firstOrFail();

            $doc = CoachingDoc::where('id_usulan', $idUsulan)->first();

            $deletedFiles = [];

            if ($doc) {
                if ($doc->doc_pelaksanaan) {
                    $deletedFiles[] = $doc->doc_pelaksanaan;
                }

                if ($doc->doc_evaluasi) {
                    $deletedFiles[] = $doc->doc_evaluasi;
                }

                $doc->delete();
            }

            $bangkom->delete();

            // File dihapus SETELAH DB commit
            DB::afterCommit(function () use ($deletedFiles) {
                foreach ($deletedFiles as $file) {
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            });
        });
    }

    protected function handleUpload(
        array $payload,
        CoachingDoc $doc,
        string $input,
        string $column,
        array &$deletedFiles
    ): void {
        if (!isset($payload[$input])) {
            return;
        }

        $file = $payload[$input];

        if ($file && $file->isValid()) {

            if (!empty($doc->{$column})) {
                $deletedFiles[] = $doc->{$column};
            }

            $doc->{$column} = $file->store('bukti_dukung', 'public');
        }
    }

    public function getDetail(int|string $id): array
    {
        $usulan = BangkomData::with('bangkomDoc')
            ->Where('id_usulan', $id)
            ->firstOrFail();

        return [
            'id_usulan'       => $usulan->id_usulan,
            'id_diklat'       => $usulan->id_diklat,
            'namakegiatan'    => $usulan->namakegiatan,
            'institusi'       => $usulan->institusi,
            'tanggal_mulai'   => $usulan->tanggal_mulai,
            'tanggal_selesai' => $usulan->tanggal_selesai,
            'status'          => $usulan->status,

            'dokumen_pelaksanaan_url' =>
            $usulan->bangkomDoc?->doc_pelaksanaan
                ? asset('storage/' . $usulan->bangkomDoc->doc_pelaksanaan)
                : null,

            'dokumen_evaluasi_url' =>
            $usulan->bangkomDoc?->doc_evaluasi
                ? asset('storage/' . $usulan->bangkomDoc->doc_evaluasi)
                : null,
        ];
    }

    public function ajukan(int|string $id): BangkomData
    {
        return DB::transaction(function () use ($id) {

            $bangkom = BangkomData::Where('id_usulan', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bangkom->nip !== Auth::user()->email) {
                throw new \Exception('Tidak berhak mengajukan data ini');
            }

            if ($bangkom->status === 9) {
                throw new \Exception('Usulan sudah diajukan');
            }

            if (!in_array($bangkom->status, [8])) {
                throw new \Exception('Status tidak valid untuk diajukan');
            }

            $bangkom->status = 9;
            $bangkom->save();

            return $bangkom;
        });
    }

    public function datatable()
    {
        $query = BangkomData::with('bangkomDoc')
            ->where('jenis', '!=', 0)
            ->where('nip', Auth::user()->email);

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('jenis', fn($row) => match ($row->jenis) {
                2 => 'Coaching',
                1 => 'Mentoring',
                default => $row->jenis,
            })

            ->editColumn('status', function ($row) {
                return match (true) {
                    $row->status === 8 =>
                    '<span class="badge rounded-pill bg-label-primary">Draft</span>',

                    $row->status === 9 =>
                    '<span class="badge rounded-pill bg-primary">Proses Verifikasi BKPSDM</span>',

                    $row->status === 0 =>
                    '<span class="badge rounded-pill bg-label-danger">Ditolak</span>
                         <br><span class="text-danger small">*: ' . e($row->keterangan) . '</span>',

                    default => $row->status,
                };
            })

            ->addColumn('action', function ($row) {
                return $this->actionButtons($row);
            })

            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    // =========================
    // ACTION BUTTON BUILDER
    // =========================
    protected function actionButtons($row): string
    {
        $buttons = [];

        if (in_array($row->status, [8, 0])) {
            $buttons[] = $this->btnAjukan($row->id_usulan);
            $buttons[] = $this->btnDetail($row->id_usulan);
            $buttons[] = $this->btnEdit($row->id_usulan);
            $buttons[] = $this->btnDelete($row->id_usulan);
        }

        if ($row->status === 9) {
            $buttons[] = $this->btnDetail($row->id_usulan);
        }

        return implode(' ', $buttons);
    }

    protected function btnAjukan($id): string
    {
        return '<button class="btn btn-sm btn-success btn-ajukan" data-id="' . $id . '">
                    <i class="bx bx-forward-big"></i>
                </button>';
    }

    protected function btnDetail($id): string
    {
        return '<button class="btn btn-sm btn-info view-detail" data-id="' . $id . '">
                    <i class="bx bx-eye"></i>
                </button>';
    }

    protected function btnEdit($id): string
    {
        return '<button class="btn btn-sm btn-primary btn-edit" data-id="' . $id . '">
                    <i class="bx bx-edit"></i>
                </button>';
    }

    protected function btnDelete($id): string
    {
        return '<button onclick="deleteCoaching(\'' . $id . '\')" 
                    class="btn btn-sm btn-danger">
                    <i class="bx bx-trash"></i>
                </button>';
    }
}
