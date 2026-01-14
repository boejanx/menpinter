<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'bangkom_participant';
    protected $primaryKey = 'id_participant';
    public $incrementing = false;
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_participant',
        'id_event',
        'id_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function bangkom()
    {
        return $this->belongsTo(Bangkom::class, 'id_event', 'event_id');
    }
}
