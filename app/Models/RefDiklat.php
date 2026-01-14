<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefDiklat extends Model
{
    protected $table = 'bangkom_ref';

    protected $fillable = [
        'jenis_diklat',
        'jenis_kursus_sertipikat',
    ];

    public $timestamps = false;
}
