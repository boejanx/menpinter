<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefStruktural extends Model
{
    protected $table = 'bangkom_struktural';

    protected $fillable = [
        'nama',
    ];

    public $timestamps = false;
}
