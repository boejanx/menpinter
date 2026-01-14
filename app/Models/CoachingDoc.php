<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingDoc extends Model
{
    protected $primaryKey = 'id_dokumen';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'bangkom_doc';

    protected $fillable = ['id_usulan', 'id_dokumen', 'doc_pelaksanaan', 'doc_evaluasi'];

    // Relasi ke Coment
    public function coment()
    {
        return $this->belongsTo(BangkomData::class, 'id_usulan', 'id_usulan');
    }
}
