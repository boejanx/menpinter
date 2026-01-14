<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BangkomData extends Model
{
    protected $table = 'bangkom_data';
    protected $primaryKey = 'id_usulan';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id_usulan',
        'jenis',
        'nip',
        'id_siasn',
        'id_instansi',
        'institusi',
        'id_diklat',
        'jenis_kursus',
        'jenis_sertifikat',
        'jumlah_jp',
        'id_lokasi',
        'namakegiatan',
        'nomor_sertifikat',
        'id_pns',
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'bobot',
        'doc_sertifikat',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'keterangan',
        'doc_bukti',
    ];

    public static function getAllSummary()
    {
        return self::select('nip')
            ->selectRaw("
                SUM(CASE WHEN id_diklat = 9 THEN jumlah_jp ELSE 0 END) AS jp_9,
                SUM(CASE WHEN id_diklat = 8 THEN jumlah_jp ELSE 0 END) AS jp_8,
                SUM(CASE WHEN id_diklat NOT IN (8,9) THEN jumlah_jp ELSE 0 END) AS jp_lainnya,
                SUM(jumlah_jp) AS jp_total
            ")
            ->groupBy('nip')
            ->get();
    }

    public static function getSummaryByName($name)
    {
        return self::where('nip', $name)
            ->selectRaw("
                SUM(CASE WHEN id_diklat = 9 THEN jumlah_jp ELSE 0 END) AS jp_9,
                SUM(CASE WHEN id_diklat = 8 THEN jumlah_jp ELSE 0 END) AS jp_8,
                SUM(CASE WHEN id_diklat NOT IN (8,9) THEN jumlah_jp ELSE 0 END) AS jp_lainnya,
                SUM(jumlah_jp) AS jp_total
            ")
            ->where('tahun', '>=', date('Y'))
            ->first();
    }

    public function bangkomDoc()
    {
        return $this->hasOne(CoachingDoc::class, 'id_usulan', 'id_usulan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nip');
    }

    public function ref_bangkom()
    {
        return $this->hasOne(RefDiklat::class, 'id', 'id_diklat');
    }
}
