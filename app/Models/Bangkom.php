<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bangkom extends Model
{
    protected $table = 'bangkom_schd';
    protected $primaryKey = 'event_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s'; // Format tanggal yang digunakan
    protected $dates = [
        'event_mulai',
        'event_selesai',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'event_mulai' => 'datetime',
        'event_selesai' => 'datetime',
    ];

    protected $fillable = [
        'event_id',
        'event_tema',
        'event_flyer',
        'event_register',
        'event_lokasi',
        'event_link',
        'event_mulai',
        'event_selesai',
        'event_jam_mulai',
        'event_keterangan',
        'event_jp',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getPesertaCountAttribute()
    {
        return $this->peserta()->count();
    }

    public static function pesertaCount($id)
    {
        return static::find($id)?->peserta()->count() ?? 0;
    }

    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'id_event', 'event_id');
    }

    public function getFormattedEventMulaiAttribute()
    {
        return $this->event_mulai->format('d M Y H:i');
    }

    public function getNarsum()
    {
        return $this->hasMany(Narsum::class, 'event_id', 'event_id');
    }

    public function getStatusAttribute()
    {
        if ($this->event_mulai > now()) {
            return 'akan_datang';
        }

        if ($this->event_selesai < now()) {
            return 'selesai';
        }

        return 'berlangsung';
    }

    public static function upcoming()
    {
        return self::where('event_mulai', '>', now())
            ->orderBy('event_mulai', 'asc')
            ->limit(5)
            ->get();
    }
}
