<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Narsum extends Model
{
    protected $primaryKey = 'narsum_id';
    protected $table = 'bangkom_narsum';

    protected $fillable = ['event_id', 'narsum_nama', 'narsum_jabatan', 'narsum_foto', 'created_at', 'updated_at'];

    public function Bangkom()
    {
        return $this->belongsTo(Bangkom::class, 'event_id', 'event_id');
    }
}
